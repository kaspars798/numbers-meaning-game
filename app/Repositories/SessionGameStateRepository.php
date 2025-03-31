<?php

namespace App\Repositories;

use App\Repositories\GameStateRepositoryInterface;
use Illuminate\Http\Request;


class SessionGameStateRepository implements GameStateRepositoryInterface
{
    private const IMPOSSIBLE_ANSWER = 'azgdklsr';
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getQuestions(): array
    {
        return $this->request->session()->get('questions', []);
    }

    public function addQuestion(string $question): void
    {
        $questions = $this->getQuestions();
        $questions[] = $question;
        $this->request->session()->put('questions', $questions);
    }

    public function getLastCorrectAnswer(): string
    {
        return $this->request->session()->get('lastCorrectAnswer', self::IMPOSSIBLE_ANSWER);
    }

    public function setLastCorrectAnswer(string $answer): void
    {
        $this->request->session()->put('lastCorrectAnswer', $answer);
    }

    public function reset(): void
    {
        $this->request->session()->put([
            'questions' => [],
            'lastCorrectAnswer' => self::IMPOSSIBLE_ANSWER,
        ]);
    }
}
