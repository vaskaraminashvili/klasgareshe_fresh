<?php

namespace App\Games;

/** Outcome of one tap inside an interactive question (match, label, word search). */
final readonly class StepResult
{
    /**
     * @param  array<string, mixed>  $progress
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public array $progress,
        public bool $hit,
        public array $extra = [],
    ) {}
}
