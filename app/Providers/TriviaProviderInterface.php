<?php

namespace App\Providers;

interface TriviaProviderInterface
{
    public function fetchQuestion(int $number): string;
}
