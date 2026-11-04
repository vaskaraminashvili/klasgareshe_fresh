<?php

namespace App\Games\Handlers;

use App\Games\CheckResult;
use App\Games\StepResult;
use App\Models\Question;

class WordSearchHandler extends BaseHandler
{
    public function initialProgress(Question $question): array
    {
        return ['mistakes' => 0, 'found' => [], 'cells' => []];
    }

    public function view(Question $question, array $presentation, array $progress): array
    {
        return ['grid' => $question->payload['grid'], 'words' => $question->payload['words']];
    }

    /** Input: `a` and `b` are the first and last tapped cells (row-major indexes). */
    public function step(Question $question, array $presentation, array $progress, array $input): StepResult
    {
        $grid = $question->payload['grid'];
        $size = count($grid);
        $letters = mb_str_split(implode('', $grid));
        $line = $this->line((int) ($input['a'] ?? -1), (int) ($input['b'] ?? -1), $size, count($letters));
        $word = $line ? implode('', array_map(fn (int $cell) => $letters[$cell], $line)) : '';
        $reversed = implode('', array_reverse(mb_str_split($word)));

        $hit = collect($question->payload['words'])->first(
            fn (string $target) => $word !== ''
                && ! in_array($target, $progress['found'], true)
                && ($target === $word || $target === $reversed),
        );

        if ($hit === null) {
            $progress['mistakes']++;

            return new StepResult($progress, false, ['straight' => $line !== null]);
        }

        $progress['found'][] = $hit;
        $progress['cells'] = array_values(array_unique([...$progress['cells'], ...$line]));

        return new StepResult($progress, true, ['word' => $hit]);
    }

    /** @return list<int>|null */
    private function line(int $a, int $b, int $size, int $cellCount): ?array
    {
        if ($a < 0 || $b < 0 || $a >= $cellCount || $b >= $cellCount || $a === $b) {
            return null;
        }

        $dr = intdiv($b, $size) - intdiv($a, $size);
        $dc = ($b % $size) - ($a % $size);

        if ($dr !== 0 && $dc !== 0 && abs($dr) !== abs($dc)) {
            return null;
        }

        $stride = ($dr <=> 0) * $size + ($dc <=> 0);

        return array_map(fn (int $k) => $a + $k * $stride, range(0, max(abs($dr), abs($dc))));
    }

    public function isComplete(Question $question, array $progress): bool
    {
        return count($progress['found']) === count($question->payload['words']);
    }

    public function check(Question $question, array $presentation, array $progress, mixed $input): CheckResult
    {
        return new CheckResult($this->isComplete($question, $progress) && $progress['mistakes'] <= 1);
    }
}
