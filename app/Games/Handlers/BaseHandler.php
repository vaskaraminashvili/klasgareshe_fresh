<?php

namespace App\Games\Handlers;

use App\Games\StepResult;
use App\Models\Question;

abstract class BaseHandler implements GameTypeHandler
{
    public function present(Question $question): array
    {
        return [];
    }

    public function initialProgress(Question $question): array
    {
        return ['mistakes' => 0];
    }

    public function view(Question $question, array $presentation, array $progress): array
    {
        return [];
    }

    public function step(Question $question, array $presentation, array $progress, array $input): StepResult
    {
        throw new \LogicException('This question type has no taps; use check().');
    }

    public function isComplete(Question $question, array $progress): bool
    {
        return true;
    }

    /**
     * A shuffle that never leaves an item in its original place (when there is more than one).
     *
     * @return list<int>
     */
    protected function shuffledOrder(int $count): array
    {
        $order = range(0, $count - 1);

        if ($count < 2) {
            return $order;
        }

        do {
            shuffle($order);
        } while (array_any($order, fn (int $value, int $index) => $value === $index));

        return $order;
    }

    /**
     * Random order that is not the original order (when there is more than one item).
     *
     * @return list<int>
     */
    protected function randomOrder(int $count): array
    {
        $order = range(0, $count - 1);

        if ($count < 2) {
            return $order;
        }

        do {
            shuffle($order);
        } while ($order === range(0, $count - 1));

        return $order;
    }
}
