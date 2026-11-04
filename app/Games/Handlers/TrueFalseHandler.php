<?php

namespace App\Games\Handlers;

use App\Games\CheckResult;
use App\Models\Question;

class TrueFalseHandler extends BaseHandler
{
    public function view(Question $question, array $presentation, array $progress): array
    {
        return ['options' => [
            ['label' => 'მართალია', 'icon' => 'i-thumb-up'],
            ['label' => 'არასწორია', 'icon' => 'i-thumb-down'],
        ]];
    }

    /** Card 0 is "true", card 1 is "false". */
    public function check(Question $question, array $presentation, array $progress, mixed $input): CheckResult
    {
        $answerPosition = $question->payload['answer'] ? 0 : 1;

        return new CheckResult($input === $answerPosition, ['answer' => $answerPosition]);
    }
}
