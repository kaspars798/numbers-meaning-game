<?php

namespace App\Providers;

use App\Repositories\GameStateRepositoryInterface;
use App\Repositories\SessionGameStateRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TriviaProviderInterface::class, NumbersApiTriviaProvider::class);
        $this->app->bind(GameStateRepositoryInterface::class, SessionGameStateRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
