<?php

namespace App\Services;

use App\Enums\GameType;

class ScoreCalculator
{
    public const BASE = 10;

    public const MINIMUM = 4;

    /**
     * Wrong answers score nothing. One slip costs 2 points; each extra riddle clue costs 2 more.
     *
     * @param  array<string, mixed>  $progress
     */
    public function forQuestion(GameType $type, bool $correct, array $progress): int
    {
        if (! $correct) {
            return 0;
        }

        $score = self::BASE - min((int) ($progress['mistakes'] ?? 0), 1) * 2;

        if ($type === GameType::Riddle) {
            $score -= (max((int) ($progress['clues'] ?? 1), 1) - 1) * 2;
        }

        return max($score, self::MINIMUM);
    }
}
