<?php

namespace App\Games\Handlers;

use App\Games\CheckResult;
use App\Games\StepResult;
use App\Models\Question;

class LabelHandler extends BaseHandler
{
    public function present(Question $question): array
    {
        return ['order' => $this->randomOrder(count($question->payload['spots']))];
    }

    public function initialProgress(Question $question): array
    {
        return ['mistakes' => 0, 'step' => 0, 'done' => []];
    }

    public function view(Question $question, array $presentation, array $progress): array
    {
        $spots = $question->payload['spots'];

        return [
            'picture' => $question->payload['picture'] ?? null,
            'image' => $question->image_path,
            'alt' => $question->payload['alt'] ?? '',
            'dots' => array_map(fn (array $spot) => ['x' => $spot['x'], 'y' => $spot['y']], $spots),
            'names' => array_map(fn (int $original) => $spots[$original]['label'], $presentation['order']),
        ];
    }

    /** Input: `p` = position of the tapped name. It is right when it names the spot the dot is on. */
    public function step(Question $question, array $presentation, array $progress, array $input): StepResult
    {
        $position = (int) ($input['p'] ?? -1);

        if (in_array($position, $progress['done'], true) || ! isset($presentation['order'][$position])) {
            return new StepResult($progress, false, ['ignored' => true]);
        }

        if ($presentation['order'][$position] === $progress['step']) {
            $progress['step']++;
            $progress['done'][] = $position;

            return new StepResult($progress, true);
        }

        $progress['mistakes']++;

        return new StepResult($progress, false);
    }

    public function isComplete(Question $question, array $progress): bool
    {
        return $progress['step'] === count($question->payload['spots']);
    }

    public function check(Question $question, array $presentation, array $progress, mixed $input): CheckResult
    {
        return new CheckResult($this->isComplete($question, $progress) && $progress['mistakes'] <= 1);
    }
}
