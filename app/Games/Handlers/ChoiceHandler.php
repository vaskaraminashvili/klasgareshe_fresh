<?php

namespace App\Games\Handlers;

use App\Games\CheckResult;
use App\Models\Question;

/** Shared by every "pick one card" game: options are shuffled once per session. */
abstract class ChoiceHandler extends BaseHandler
{
    public function present(Question $question): array
    {
        return ['order' => $this->randomOrder(count($question->payload['options']))];
    }

    public function view(Question $question, array $presentation, array $progress): array
    {
        $options = $question->payload['options'];

        return [
            'options' => array_map(
                fn (int $original) => is_array($options[$original]) ? $options[$original] : ['label' => $options[$original]],
                $presentation['order'],
            ),
        ];
    }

    public function check(Question $question, array $presentation, array $progress, mixed $input): CheckResult
    {
        $order = $presentation['order'];
        $picked = is_int($input) ? $input : -1;

        return new CheckResult(
            correct: ($order[$picked] ?? null) === $question->payload['answer'],
            reveal: ['answer' => array_search($question->payload['answer'], $order, true)],
        );
    }
}
