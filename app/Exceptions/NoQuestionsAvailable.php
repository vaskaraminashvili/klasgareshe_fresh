<?php

namespace App\Exceptions;

use RuntimeException;

class NoQuestionsAvailable extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('ამ დონეზე კითხვები ჯერ არ არის.');
    }
}
