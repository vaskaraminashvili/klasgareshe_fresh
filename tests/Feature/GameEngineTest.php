<?php

use App\Enums\GameType;
use App\Enums\SessionStatus;
use App\Exceptions\NoQuestionsAvailable;
use App\Models\Game;
use App\Models\GameSession;
use App\Models\GameSessionQuestion;
use App\Models\Player;
use App\Models\Question;
use App\Services\GameSessionService;
use Database\Seeders\GameSeeder;
use Database\Seeders\QuestionSeeder;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed([GameSeeder::class, QuestionSeeder::class]);
    $this->player = Player::create(['token' => (string) Str::uuid()]);
    $this->service = app(GameSessionService::class);
});

/** Plays the current question through the real service API, the way a child would. */
function solveCurrent(GameSession $session, GameSessionService $service, bool $correct = true): array
{
    $item = $service->currentItem($session);
    $question = $item->question;
    $payload = $question->payload;
    $presentation = $item->presentation;

    switch ($question->game->type) {
        case GameType::Multiple:
        case GameType::Picture:
        case GameType::Riddle:
            $answer = array_search($payload['answer'], $presentation['order'], true);
            $pick = $correct ? $answer : collect(array_keys($presentation['order']))->first(fn ($p) => $p !== $answer);

            return $service->check($session, $pick);
        case GameType::TrueFalse:
            return $service->check($session, ($payload['answer'] xor ! $correct) ? 0 : 1);
        case GameType::Gap:
            $shown = array_map(fn ($i) => $payload['words'][$i], $presentation['order']);
            $target = $correct ? $payload['answer'] : collect($payload['words'])->first(fn ($w) => $w !== $payload['answer']);

            return $service->check($session, array_search($target, $shown, true));
        case GameType::Match:
            foreach ($presentation['right_order'] as $rightPos => $left) {
                $service->step($session, ['l' => $left, 'r' => $rightPos]);
            }

            return $service->check($session, null);
        case GameType::Label:
            foreach (array_keys($payload['spots']) as $spot) {
                $service->step($session, ['p' => array_search($spot, $presentation['order'], true)]);
            }

            return $service->check($session, null);
        case GameType::WordSearch:
            $letters = mb_str_split(implode('', $payload['grid']));
            foreach ($payload['words'] as $word) {
                $cells = findWord($letters, count($payload['grid']), $word);
                $service->step($session, ['a' => $cells[0], 'b' => end($cells)]);
            }

            return $service->check($session, null);
    }
}

/** @return list<int> */
function findWord(array $letters, int $size, string $word): array
{
    $chars = mb_str_split($word);

    foreach ([$chars, array_reverse($chars)] as $sequence) {
        foreach (array_keys($letters) as $start) {
            foreach ([[0, 1], [1, 0], [1, 1], [1, -1]] as [$dr, $dc]) {
                $cells = [];
                foreach ($sequence as $k => $char) {
                    $r = intdiv($start, $size) + $dr * $k;
                    $c = $start % $size + $dc * $k;
                    if ($r < 0 || $r >= $size || $c < 0 || $c >= $size || $letters[$r * $size + $c] !== $char) {
                        continue 2;
                    }
                    $cells[] = $r * $size + $c;
                }

                return $cells;
            }
        }
    }

    throw new RuntimeException("Word $word not in grid");
}

it('generates a session with questions from the right game', function (GameType $type) {
    $game = Game::where('type', $type->value)->first();
    $session = $this->service->start($this->player, $game, 5);

    expect($session->status)->toBe(SessionStatus::Active)
        ->and($session->difficulty)->toBe(5)
        ->and($session->question_ids)->toHaveCount($session->total_questions)
        ->and($session->items)->toHaveCount($session->total_questions)
        ->and(Question::whereIn('id', $session->question_ids)->pluck('game_id')->unique()->all())->toBe([$game->id]);
})->with(fn () => GameType::cases());

it('selects questions of the requested difficulty', function () {
    $session = $this->service->start($this->player, Game::where('type', 'multiple')->first(), 3);

    expect(Question::whereIn('id', $session->question_ids)->pluck('difficulty')->unique()->all())->toBe([3]);
});

it('falls back to the nearest difficulty that has content', function () {
    $session = $this->service->start($this->player, Game::where('type', 'riddle')->first(), 3);

    expect(Question::whereIn('id', $session->question_ids)->pluck('difficulty')->unique()->all())->toBe([2]);
});

it('builds a mixed session with one question per game type', function () {
    $session = $this->service->start($this->player, null, 5);
    $types = Question::with('game')->whereIn('id', $session->question_ids)->get()
        ->map(fn ($q) => $q->game->type->value)->sort()->values()->all();

    expect($types)->toBe(collect(GameType::cases())->map->value->sort()->values()->all());
});

it('randomizes questions and avoids the previous session when the pool allows', function () {
    $game = Game::where('type', 'multiple')->first();
    $game->update(['config' => [...$game->config, 'questions_per_session' => 5]]);

    $first = $this->service->start($this->player, $game->fresh(), 4);
    $second = $this->service->start($this->player, $game->fresh(), 4);

    expect(array_intersect($first->question_ids, $second->question_ids))->toBeEmpty();
});

it('shuffles answer order, stores it, and keeps it after a refresh', function () {
    $session = $this->service->start($this->player, Game::where('type', 'multiple')->first(), 5);

    expect($session->items->first()->presentation['order'])->not->toBe([0, 1, 2, 3]);

    $before = $this->service->questionState($this->service->currentItem($session));
    $after = $this->service->questionState(app(GameSessionService::class)->currentItem(GameSession::find($session->id)));

    expect($after)->toBe($before);
});

it('never sends answers to the browser', function (GameType $type) {
    $session = $this->service->start($this->player, Game::where('type', $type->value)->first(), 5);
    $state = $this->service->questionState($this->service->currentItem($session));

    expect(json_encode($state))->not->toContain('"answer"');
})->with(fn () => GameType::cases());

it('validates correct and incorrect answers for every game type', function (GameType $type) {
    $game = Game::where('type', $type->value)->first();
    $good = $this->service->start($this->player, $game, 5);

    expect(solveCurrent($good, $this->service)['correct'])->toBeTrue();

    if (in_array($type, [GameType::Multiple, GameType::Picture, GameType::TrueFalse, GameType::Gap, GameType::Riddle], true)) {
        $bad = $this->service->start($this->player, $game, 5);
        expect(solveCurrent($bad, $this->service, correct: false)['correct'])->toBeFalse();
    }
})->with(fn () => GameType::cases());

it('counts a match question as wrong after two slips', function () {
    $session = $this->service->start($this->player, Game::where('type', 'match')->first(), 5);
    $right = $this->service->currentItem($session)->presentation['right_order'];

    $wrongPos = array_search(1, $right, true);
    $this->service->step($session, ['l' => 0, 'r' => $wrongPos]);
    $this->service->step($session, ['l' => 0, 'r' => $wrongPos]);
    foreach ($right as $pos => $left) {
        $this->service->step($session, ['l' => $left, 'r' => $pos]);
    }

    expect($this->service->check($session, null)['correct'])->toBeFalse();
});

it('rejects Check before an interactive question is finished', function () {
    $session = $this->service->start($this->player, Game::where('type', 'wordsearch')->first(), 5);

    expect($this->service->check($session, null))->toBe(['error' => 'incomplete']);
});

it('completes the session, scores it and measures time', function () {
    $this->travelTo(now());
    $session = $this->service->start($this->player, Game::where('type', 'truefalse')->first(), 2);

    $this->travel(95)->seconds();
    while ($this->service->currentItem($session)) {
        solveCurrent($session, $this->service);
    }

    $session->refresh();
    expect($session->status)->toBe(SessionStatus::Completed)
        ->and($session->correct_count)->toBe($session->total_questions)
        ->and($session->incorrect_count)->toBe(0)
        ->and($session->score)->toBe($session->total_questions * 10)
        ->and($session->duration_seconds)->toBe(95)
        ->and($session->accuracyPercent())->toBe(100)
        ->and($session->stars())->toBe(3);
});

it('counts wrong answers and gives no points for them', function () {
    $session = $this->service->start($this->player, Game::where('type', 'multiple')->first(), 1);

    solveCurrent($session, $this->service, correct: false);
    while ($this->service->currentItem($session)) {
        solveCurrent($session, $this->service);
    }

    $session->refresh();
    expect($session->incorrect_count)->toBe(1)
        ->and($session->correct_count)->toBe($session->total_questions - 1)
        ->and($session->score)->toBe(($session->total_questions - 1) * 10);
});

it('takes points off for extra riddle clues', function () {
    $session = $this->service->start($this->player, Game::where('type', 'riddle')->first(), 5);

    $this->service->revealClue($session);
    $this->service->revealClue($session);
    $result = solveCurrent($session, $this->service);

    $item = GameSessionQuestion::where('game_session_id', $session->id)->orderBy('position')->first();
    expect($result['correct'])->toBeTrue()
        ->and($item->clues_used)->toBe(3)
        ->and($item->score)->toBe(6);
});

it('reveals riddle clues one at a time', function () {
    $session = $this->service->start($this->player, Game::where('type', 'riddle')->first(), 5);

    expect($this->service->questionState($this->service->currentItem($session))['clues'])->toHaveCount(1);
    expect($this->service->revealClue($session)['clues'])->toHaveCount(2);
});

it('works with a pool smaller than the session size', function () {
    $session = $this->service->start($this->player, Game::where('type', 'label')->first(), 5);

    expect($session->total_questions)->toBe(1);
});

it('refuses to start when a game has no questions at all', function () {
    $game = Game::where('type', 'label')->first();
    $game->questions()->delete();

    $this->service->start($this->player, $game, 5);
})->throws(NoQuestionsAvailable::class);

it('only picks questions of the chosen subject and remembers the filter on the session', function () {
    $game = Game::where('type', 'multiple')->first();
    Question::create(['game_id' => $game->id, 'subject' => 'history', 'grade' => 7, 'difficulty' => 5, 'prompt' => 'რომელ წელს?', 'payload' => ['options' => ['1121', '1122'], 'answer' => 0], 'praise' => 'სწორია!', 'hint' => '1121.']);

    $session = $this->service->start($this->player, $game, 5, 'history', 7);

    expect($session->items()->with('question')->get()->pluck('question.subject')->unique()->all())->toBe(['history'])
        ->and($session->subject)->toBe('history')
        ->and($session->grade)->toBe(7);
});

it('treats questions without a grade as fitting every grade', function () {
    $session = $this->service->start($this->player, Game::where('type', 'multiple')->first(), 5, 'math', 9);

    expect($session->total_questions)->toBeGreaterThan(0);
});

it('refuses to start when the chosen subject has no questions', function () {
    $this->service->start($this->player, Game::where('type', 'multiple')->first(), 5, 'chemistry');
})->throws(NoQuestionsAvailable::class);
