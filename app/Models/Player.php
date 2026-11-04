<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable(['token', 'name'])]
class Player extends Model
{
    public function sessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    /** Consecutive days with a finished game, ending today (or yesterday, if today is still empty). */
    public function streak(): int
    {
        $days = $this->playedDays(60);
        $day = $days->contains(now()->toDateString()) ? now() : now()->subDay();
        $streak = 0;

        while ($days->contains($day->toDateString())) {
            $streak++;
            $day = $day->copy()->subDay();
        }

        return $streak;
    }

    /**
     * Whether a game was finished on each of the last `$count` days, oldest first, today last.
     *
     * @return list<bool>
     */
    public function activeDays(int $count): array
    {
        $days = $this->playedDays($count);

        return array_map(
            fn (int $back) => $days->contains(now()->subDays($back)->toDateString()),
            range($count - 1, 0),
        );
    }

    /** @return Collection<int, string> */
    private function playedDays(int $count): Collection
    {
        return $this->sessions()
            ->where('status', SessionStatus::Completed)
            ->where('completed_at', '>=', now()->subDays($count)->startOfDay())
            ->pluck('completed_at')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->values();
    }
}
