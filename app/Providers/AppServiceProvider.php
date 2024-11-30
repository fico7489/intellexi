<?php

namespace App\Providers;

use App\ESModule\Cdc\Consumer\RedisConsumer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->commands([
            RedisConsumer::class,
        ]);

        //app(EloquentListener::class)->listen();
    }
}
