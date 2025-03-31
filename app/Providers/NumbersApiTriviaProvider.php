<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;

class NumbersApiTriviaProvider implements TriviaProviderInterface
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = config('trivia.api_endpoint', 'http://numbersapi.com');
    }

    public function fetchQuestion(int $number): string
    {
        $response = Http::get("{$this->endpoint}/{$number}/trivia", [
            'fragment' => true,
            'default' => 'NotFound',
        ]);

        return $response->successful() ? $response->body() : throw new \RuntimeException('Trivia fetch failed: ' . $response->status());
    }
}
