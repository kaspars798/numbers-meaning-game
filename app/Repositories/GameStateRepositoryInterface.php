<?php

namespace App\Repositories;

interface GameStateRepositoryInterface
{
    public function getQuestions(): array;
    public function addQuestion(string $question): void;
    public function getLastCorrectAnswer(): string;
    public function setLastCorrectAnswer(string $answer): void;
    public function reset(): void;
}
