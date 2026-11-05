<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['uuid', 'player_id', 'game_id', 'subject', 'grade', 'difficulty', 'status', 'started_at', 'completed_at', 'duration_seconds', 'total_questions', 'correct_count', 'incorrect_count', 'clues_used', 'score', 'question_ids'])]
class GameSession extends Model
{
    protected function casts(): array
    {
        return [
            'status' => SessionStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'question_ids' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GameSessionQuestion::class)->orderBy('position');
    }

    public function stars(): int
    {
        return match (true) {
            $this->accuracyPercent() >= 90 => 3,
            $this->accuracyPercent() >= 60 => 2,
            default => 1,
        };
    }

    public function accuracyPercent(): int
    {
        return $this->total_questions > 0 ? (int) round($this->correct_count / $this->total_questions * 100) : 0;
    }
}
