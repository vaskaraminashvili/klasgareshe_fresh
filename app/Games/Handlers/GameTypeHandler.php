<?php

namespace App\Games\Handlers;

use App\Games\CheckResult;
use App\Games\StepResult;
use App\Models\Question;

/**
 * One handler per game type. Everything secret (answers, shuffles) stays server-side;
 * view() returns only what the browser may see.
 */
interface GameTypeHandler
{
    /**
     * Frozen when the session is created: shuffles live here so a refresh never reorders anything.
     *
     * @return array<string, mixed>
     */
    public function present(Question $question): array;

    /** @return array<string, mixed> */
    public function initialProgress(Question $question): array;

    /**
     * @param  array<string, mixed>  $presentation
     * @param  array<string, mixed>  $progress
     * @return array<string, mixed>
     */
    public function view(Question $question, array $presentation, array $progress): array;

    /**
     * @param  array<string, mixed>  $presentation
     * @param  array<string, mixed>  $progress
     * @param  array<string, mixed>  $input
     */
    public function step(Question $question, array $presentation, array $progress, array $input): StepResult;

    /**
     * @param  array<string, mixed>  $presentation
     * @param  array<string, mixed>  $progress
     */
    public function check(Question $question, array $presentation, array $progress, mixed $input): CheckResult;

    /**
     * Whether Check can be pressed yet (all pairs matched, all words found, ...).
     *
     * @param  array<string, mixed>  $progress
     */
    public function isComplete(Question $question, array $progress): bool;
}
