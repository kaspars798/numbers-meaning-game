<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    const ENDPOINT = 'http://numbersapi.com';
    const MAX_NUMBER = 100;
    const ANSWERS_COUNT = 5;
    const IMPOSSIBLE_ANSWER = 'azgdklsr';

    private function getQuestion(int $number): string
    {
        $response = Http::withUrlParameters([
            'endpoint' => self::ENDPOINT,
            'number' => $number
        ])->get('{+endpoint}/{number}/trivia?fragment&default=NotFound');

        if ($response->successful()) {
            return $response->body();
        }

        throw new \RuntimeException($response->clientError());
    }

    private function generateAnswers(int $correctAnswer): array
    {
        if (self::ANSWERS_COUNT > self::MAX_NUMBER) {
            throw new \RuntimeException('answers_count must not be greater than max_number');
        }

        $answers = [$correctAnswer];

        while (count($answers) < self::ANSWERS_COUNT) {
            $repeat = false;

            do {
                $newAnswer = rand(1,self::MAX_NUMBER);
                if (in_array($newAnswer, $answers)) {
                    $repeat = true;
                } else {
                    $answers[] = $newAnswer;
                    $repeat = false;
                }
            } while ($repeat);

        }

        shuffle($answers);

        return $answers;
    }

    public function playTheGame(Request $request): Response
    {
        if ($request->start) {
            $this->endTheGame($request);
        }

        $repeat = false;
        $randomAnswer = $request->session()->get('lastCorrectAnswer', self::IMPOSSIBLE_ANSWER);
        $randomAnswers = [];
        $question = '';
        $questions = $request->session()->get('questions', []);

        if (
            count($questions)
            && !empty($request->answer)
            && $randomAnswer != self::IMPOSSIBLE_ANSWER
            && $request->answer != $randomAnswer
        ) {

            $question = $questions[count($questions) - 1];
            $randomAnswers = $this->generateAnswers($randomAnswer);

        } else if (count($questions) === 0) {

            $randomAnswer = rand(1,self::MAX_NUMBER);
            $question = $this->getQuestion($randomAnswer);
            $questions[] = $question;
            $request->session()->put('questions', $questions);
            $randomAnswers = $this->generateAnswers($randomAnswer);

        } else if (count($questions) === 10) {

            $question = '';
            $randomAnswer = self::IMPOSSIBLE_ANSWER;
            $randomAnswers = [];
            $questionCount = 11;
            $this->endTheGame($request);

        } else {

            do {
                do {
                    $randomAnswer = rand(1,self::MAX_NUMBER);
                    $question = $this->getQuestion($randomAnswer);
                } while ($question == 'NotFound');
                
                if (in_array($question, $questions)) {
                    $repeat = true;
                } else {
                    $questions = $request->session()->get('questions', []);
                    $questions[] = $question;
                    $request->session()->put('questions', $questions);
                    $repeat = false;
                }
                

            } while ($repeat);
            
            $randomAnswers = $this->generateAnswers($randomAnswer);
        }

        $request->session()->put('lastCorrectAnswer', $randomAnswer);

        return Inertia::render('Dashboard', [
            'question' => $question,
            'questionNo' => $questionCount ?? count( $request->session()->get('questions', []) ),
            'correctAnswer' => $randomAnswer,
            'allAnswers' => $randomAnswers,
        ]);
    }

    public function endTheGame(Request $request) : void 
    {
        $request->session()->put('questions', []);
        $request->session()->put('lastCorrectAnswer', 0);
    }
}
