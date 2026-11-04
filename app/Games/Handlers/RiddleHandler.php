<?php

namespace App\Games\Handlers;

use App\Models\Question;

class RiddleHandler extends ChoiceHandler
{
    public function initialProgress(Question $question): array
    {
        return ['mistakes' => 0, 'clues' => 1];
    }

    public function view(Question $question, array $presentation, array $progress): array
    {
        $clues = $question->payload['clues'];

        return parent::view($question, $presentation, $progress) + [
            'clues' => array_slice($clues, 0, $progress['clues'] ?? 1),
            'clue_total' => count($clues),
        ];
    }
}
