<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['game_session_id', 'question_id', 'position', 'presentation', 'progress', 'mistakes', 'clues_used', 'is_correct', 'score', 'answered_at'])]
class GameSessionQuestion extends Model
{
    protected function casts(): array
    {
        return ['presentation' => 'array', 'progress' => 'array', 'is_correct' => 'boolean', 'answered_at' => 'datetime'];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(GameSession::class, 'game_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
