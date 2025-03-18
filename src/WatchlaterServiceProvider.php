<?php

namespace Animelhd\AnimesWatchlater;

use Illuminate\Support\ServiceProvider;

class WatchlaterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            \dirname(__DIR__).'/config/animeswatchlater.php' => config_path('animeswatchlater.php'),
        ], 'watchlater-config');

        $this->publishes([
            \dirname(__DIR__).'/migrations/' => database_path('migrations'),
        ], 'watchlater-migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__).'/config/animeswatchlater.php',
            'watchlater'
        );
    }
}
