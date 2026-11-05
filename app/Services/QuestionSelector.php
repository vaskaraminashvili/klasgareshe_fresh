<?php

namespace App\Services;

use App\Models\Game;
use App\Models\GameSession;
use App\Models\Player;
use App\Models\Question;
use Illuminate\Support\Collection;

/** Random, difficulty-aware question picking that avoids repeating the previous session. */
class QuestionSelector
{
    /**
     * One game: `$count` questions of that game. No game: one question per active game ("daily mission").
     *
     * @return Collection<int, Question>
     */
    public function select(?Game $game, int $difficulty, int $count, ?Player $player = null, ?string $subject = null, ?int $grade = null): Collection
    {
        $exclude = $this->previousQuestionIds($game, $player);

        $ids = $game
            ? $this->pick($this->pool($game->id, $difficulty, $subject, $grade), $count, $exclude)
            : Game::where('is_active', true)->pluck('id')
                ->flatMap(fn (int $gameId) => $this->pick($this->pool($gameId, $difficulty, $subject, $grade), 1, $exclude))
                ->all();

        shuffle($ids);

        $questions = Question::whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(fn (int $id) => $questions[$id])->values();
    }

    /**
     * Questions of the requested level; if that level is empty, the closest level that has any.
     * Subject must match exactly; a question without a grade fits every grade.
     *
     * @return list<int>
     */
    private function pool(int $gameId, int $difficulty, ?string $subject, ?int $grade): array
    {
        $active = Question::where('game_id', $gameId)->where('is_active', true)
            ->when($subject, fn ($query) => $query->where('subject', $subject))
            ->when($grade, fn ($query) => $query->where(fn ($query) => $query->where('grade', $grade)->orWhereNull('grade')));

        $level = (clone $active)->distinct()->pluck('difficulty')
            ->sortBy(fn (int $level) => [abs($level - $difficulty), $level])
            ->first();

        return $level === null ? [] : (clone $active)->where('difficulty', $level)->pluck('id')->all();
    }

    /**
     * Fresh questions first; if there are not enough, top up with previously seen ones.
     *
     * @param  list<int>  $pool
     * @param  list<int>  $exclude
     * @return list<int>
     */
    private function pick(array $pool, int $count, array $exclude): array
    {
        shuffle($pool);

        $fresh = array_values(array_diff($pool, $exclude));
        $seen = array_values(array_intersect($pool, $exclude));

        return array_slice([...$fresh, ...$seen], 0, $count);
    }

    /** @return list<int> */
    private function previousQuestionIds(?Game $game, ?Player $player): array
    {
        if (! $player) {
            return [];
        }

        $previous = GameSession::where('player_id', $player->id)
            ->when($game, fn ($query) => $query->where('game_id', $game->id), fn ($query) => $query->whereNull('game_id'))
            ->latest('id')
            ->first();

        return $previous?->question_ids ?? [];
    }
}
