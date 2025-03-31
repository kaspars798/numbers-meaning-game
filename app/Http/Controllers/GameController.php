<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Providers\TriviaProviderInterface;
use App\Repositories\GameStateRepositoryInterface;

class GameController extends Controller
{
    private TriviaProviderInterface $triviaProvider;
    private GameStateRepositoryInterface $gameState;

    public function __construct(
        TriviaProviderInterface $triviaProvider, 
        GameStateRepositoryInterface $gameState
    ) {
        $this->triviaProvider = $triviaProvider;
        $this->gameState = $gameState;
    }

    private function generateAnswerOptions(int $correctAnswer): array
    {
        $answersCount = config('game.answers_count');
        $maxNumber = config('game.max_number');

        if ($answersCount > $maxNumber) {
            throw new \RuntimeException('ANSWERS_COUNT must not exceed MAX_NUMBER');
        }

        $answers = [$correctAnswer];
        while (count($answers) < $answersCount) {
            $newAnswer = rand(1, $maxNumber);
            if (!in_array($newAnswer, $answers)) {
                $answers[] = $newAnswer;
            }
        }

        shuffle($answers);
        return $answers;
    }

    private function generateNewQuestion(): array
    {
        $questions = $this->gameState->getQuestions();
        do {
            $correctAnswer = rand(1, config('game.max_number'));
            $question = $this->triviaProvider->fetchQuestion($correctAnswer);
        } while ($question === 'NotFound' || in_array($question, $questions));

        $this->gameState->addQuestion($question);
        return [$question, $correctAnswer];
    }

    public function play(Request $request): Response
    {
        $questions = $this->gameState->getQuestions();
        $lastCorrectAnswer = $this->gameState->getLastCorrectAnswer();

        if ($request->input('start') || count($questions) >= config('game.max_questions')) {
            $this->gameState->reset();
            $questions = [];
            $lastCorrectAnswer = config('game.impossible_answer');
        }

        $question = '';
        $correctAnswer = $lastCorrectAnswer;
        $answerOptions = [];

        if (count($questions) === 0) {
            [$question, $correctAnswer] = $this->generateNewQuestion();
            $answerOptions = $this->generateAnswerOptions($correctAnswer);
        } elseif ($request->has('answer') && $correctAnswer !== config('game.impossible_answer')) {
            $question = end($questions);
            $answerOptions = $this->generateAnswerOptions($correctAnswer);

            if ($request->input('answer') == $correctAnswer) {
                [$question, $correctAnswer] = $this->generateNewQuestion();
                $answerOptions = $this->generateAnswerOptions($correctAnswer);
            }
        }

        $this->gameState->setLastCorrectAnswer($correctAnswer);

        return Inertia::render('Dashboard', [
            'question' => $question,
            'questionNo' => count($questions) + 1,
            'correctAnswer' => $correctAnswer,
            'allAnswers' => $answerOptions,
        ]);
    }
}
