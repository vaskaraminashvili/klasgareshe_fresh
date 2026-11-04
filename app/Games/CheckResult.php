<?php

namespace App\Games;

/** Final verdict for a question; `reveal` is what the client may show after checking. */
final readonly class CheckResult
{
    /** @param  array<string, mixed>  $reveal */
    public function __construct(public bool $correct, public array $reveal = []) {}
}
