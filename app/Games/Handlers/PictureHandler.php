<?php

namespace App\Games\Handlers;

use App\Models\Question;

class PictureHandler extends ChoiceHandler
{
    public function view(Question $question, array $presentation, array $progress): array
    {
        return parent::view($question, $presentation, $progress) + [
            'picture' => $question->payload['picture'] ?? null,
            'image' => $question->image_path,
            'shadow' => (bool) ($question->payload['shadow'] ?? false),
            'alt' => $question->payload['alt'] ?? '',
        ];
    }
}
