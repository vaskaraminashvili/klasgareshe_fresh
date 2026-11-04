<?php

namespace App\Games\Handlers;

use App\Games\CheckResult;
use App\Models\Question;

class GapHandler extends BaseHandler
{
    public function present(Question $question): array
    {
        return ['order' => $this->randomOrder(count($question->payload['words']))];
    }

    public function view(Question $question, array $presentation, array $progress): array
    {
        $words = $question->payload['words'];

        return [
            'before' => $question->payload['before'],
            'after' => $question->payload['after'],
            'words' => array_map(fn (int $original) => $words[$original], $presentation['order']),
        ];
    }

    public function check(Question $question, array $presentation, array $progress, mixed $input): CheckResult
    {
        $words = $question->payload['words'];
        $picked = is_int($input) ? ($words[$presentation['order'][$input] ?? -1] ?? null) : null;

        return new CheckResult($picked === $question->payload['answer'], ['answer' => $question->payload['answer']]);
    }
}
