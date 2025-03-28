<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('play-game', [GameController::class, 'playTheGame'])
    ->middleware(['auth', 'verified'])->name('playGame');

Route::get('end-game', [GameController::class, 'endTheGame'])
    ->middleware(['auth', 'verified'])->name('endGame');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
