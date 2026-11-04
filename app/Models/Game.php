<?php

namespace App\Models;

use App\Enums\GameType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'name', 'description', 'config', 'is_active', 'sort'])]
class Game extends Model
{
    protected function casts(): array
    {
        return ['type' => GameType::class, 'config' => 'array', 'is_active' => 'boolean'];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
