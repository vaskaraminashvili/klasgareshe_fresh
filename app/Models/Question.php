<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['game_id', 'difficulty', 'prompt', 'image_path', 'praise', 'hint', 'payload', 'is_active'])]
class Question extends Model
{
    protected function casts(): array
    {
        return ['payload' => 'array', 'is_active' => 'boolean', 'difficulty' => 'integer'];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
