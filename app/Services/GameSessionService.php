<?php

namespace App\Services;

use App\Enums\GameType;
use App\Enums\SessionStatus;
use App\Exceptions\NoQuestionsAvailable;
use App\Models\Game;
use App\Models\GameSession;
use App\Models\GameSessionQuestion;
use App\Models\Player;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Creates, plays and finishes a game session. Game-type rules live in the handlers. */
class GameSessionService
{
    public const DEFAULT_QUESTIONS = 10;

    public function __construct(
        private readonly QuestionSelector $selector,
        private readonly ScoreCalculator $scores,
    ) {}

    /**
     * Fixes the question set and every shuffle at creation, so a refresh never changes them.
     * `$subject` and `$grade` narrow the pool; null means any.
     */
    public function start(Player $player, ?Game $game, int $difficulty, ?string $subject = null, ?int $grade = null): GameSession
    {
        $difficulty = max(1, min(10, $difficulty));
        $count = $game?->config['questions_per_session'] ?? self::DEFAULT_QUESTIONS;
        $questions = $this->selector->select($game, $difficulty, $count, $player, $subject, $grade);

        if ($questions->isEmpty()) {
            throw new NoQuestionsAvailable;
        }

        return DB::transaction(function () use ($player, $game, $difficulty, $subject, $grade, $questions) {
            $session = GameSession::create([
                'uuid' => (string) Str::uuid(),
                'player_id' => $player->id,
                'game_id' => $game?->id,
                'subject' => $subject,
                'grade' => $grade,
                'difficulty' => $difficulty,
                'status' => SessionStatus::Active,
                'started_at' => now(),
                'total_questions' => $questions->count(),
                'question_ids' => $questions->pluck('id')->all(),
            ]);

            foreach ($questions->values() as $position => $question) {
                $handler = $question->game->type->handler();

                $session->items()->create([
                    'question_id' => $question->id,
                    'position' => $position,
                    'presentation' => $handler->present($question),
                    'progress' => $handler->initialProgress($question),
                ]);
            }

            return $session;
        });
    }

    public function currentItem(GameSession $session): ?GameSessionQuestion
    {
        return $session->items()->whereNull('answered_at')->with('question.game')->first();
    }

    /**
     * What the browser may know about one question. Answers are never included.
     *
     * @return array<string, mixed>
     */
    public function questionState(GameSessionQuestion $item): array
    {
        $question = $item->question;
        $type = $question->game->type;
        $handler = $type->handler();
        $progress = $item->progress;

        return [
            'position' => $item->position,
            'type' => $type->value,
            'game' => $type->label(),
            'prompt' => $question->prompt,
            'complete' => $handler->isComplete($question, $progress),
            'progress' => collect($progress)->except('result')->all(),
            ...$handler->view($question, $item->presentation, $progress),
        ];
    }

    /**
     * One tap in match / label / word search.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function step(GameSession $session, array $input): array
    {
        return DB::transaction(function () use ($session, $input) {
            $item = $this->lockedCurrentItem($session);
            $handler = $item->question->game->type->handler();
            $result = $handler->step($item->question, $item->presentation, $item->progress, $input);

            $item->update(['progress' => $result->progress, 'mistakes' => $result->progress['mistakes'] ?? 0]);

            return ['hit' => $result->hit, 'extra' => $result->extra, 'state' => $this->questionState($item->refresh()->load('question.game'))];
        });
    }

    /** Reveals the next riddle clue. */
    public function revealClue(GameSession $session): array
    {
        return DB::transaction(function () use ($session) {
            $item = $this->lockedCurrentItem($session);

            if ($item->question->game->type === GameType::Riddle) {
                $progress = $item->progress;
                $progress['clues'] = min(($progress['clues'] ?? 1) + 1, count($item->question->payload['clues']));
                $item->update(['progress' => $progress, 'clues_used' => $progress['clues']]);
            }

            return $this->questionState($item->refresh()->load('question.game'));
        });
    }

    /**
     * Final answer for the current question. Safe to call twice: the stored result is returned.
     *
     * @return array<string, mixed>
     */
    public function check(GameSession $session, mixed $input): array
    {
        return DB::transaction(function () use ($session, $input) {
            $item = $session->items()->whereNull('answered_at')->lockForUpdate()->with('question.game')->first();

            if (! $item) {
                return $this->lastResult($session);
            }

            $question = $item->question;
            $type = $question->game->type;
            $handler = $type->handler();

            if (! $handler->isComplete($question, $item->progress)) {
                return ['error' => 'incomplete'];
            }

            $result = $handler->check($question, $item->presentation, $item->progress, $input);
            $progress = $item->progress;
            $payload = [
                'correct' => $result->correct,
                'reveal' => $result->reveal,
                'feedback' => $result->correct ? $question->praise : $question->hint,
                'last' => ! $session->items()->whereNull('answered_at')->where('id', '!=', $item->id)->exists(),
            ];
            $progress['result'] = $payload;

            $item->update([
                'progress' => $progress,
                'is_correct' => $result->correct,
                'score' => $this->scores->forQuestion($type, $result->correct, $progress),
                'answered_at' => now(),
            ]);

            if ($payload['last']) {
                $this->complete($session);
            }

            return $payload;
        });
    }

    public function complete(GameSession $session): GameSession
    {
        $items = $session->items()->get();
        $correct = $items->where('is_correct', true)->count();
        $completedAt = now();

        $session->update([
            'status' => SessionStatus::Completed,
            'completed_at' => $completedAt,
            'duration_seconds' => (int) $session->started_at->diffInSeconds($completedAt, true),
            'correct_count' => $correct,
            'incorrect_count' => $items->count() - $correct,
            'clues_used' => $items->sum('clues_used'),
            'score' => $items->sum('score'),
        ]);

        return $session;
    }

    private function lockedCurrentItem(GameSession $session): GameSessionQuestion
    {
        return $session->items()->whereNull('answered_at')->lockForUpdate()->with('question.game')->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function lastResult(GameSession $session): array
    {
        return $session->items()->latest('position')->first()?->progress['result'] ?? [];
    }
}
