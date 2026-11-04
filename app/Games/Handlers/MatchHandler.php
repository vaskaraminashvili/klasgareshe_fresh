<?php

namespace App\Games\Handlers;

use App\Games\CheckResult;
use App\Games\StepResult;
use App\Models\Question;

class MatchHandler extends BaseHandler
{
    public function present(Question $question): array
    {
        return ['right_order' => $this->shuffledOrder(count($question->payload['pairs']))];
    }

    public function initialProgress(Question $question): array
    {
        return ['mistakes' => 0, 'done' => []];
    }

    public function view(Question $question, array $presentation, array $progress): array
    {
        $pairs = $question->payload['pairs'];

        return [
            'left' => array_column($pairs, 0),
            'right' => array_map(fn (int $original) => $pairs[$original][1], $presentation['right_order']),
        ];
    }

    /** Input: `l` = left index, `r` = position in the right column. */
    public function step(Question $question, array $presentation, array $progress, array $input): StepResult
    {
        $left = (int) ($input['l'] ?? -1);
        $right = (int) ($input['r'] ?? -1);

        if (isset($progress['done'][$left]) || in_array($right, $progress['done'], true)) {
            return new StepResult($progress, false, ['ignored' => true]);
        }

        if (($presentation['right_order'][$right] ?? null) === $left) {
            $progress['done'][$left] = $right;

            return new StepResult($progress, true);
        }

        $progress['mistakes']++;

        return new StepResult($progress, false);
    }

    public function isComplete(Question $question, array $progress): bool
    {
        return count($progress['done']) === count($question->payload['pairs']);
    }

    public function check(Question $question, array $presentation, array $progress, mixed $input): CheckResult
    {
        return new CheckResult($this->isComplete($question, $progress) && $progress['mistakes'] <= 1);
    }
}
