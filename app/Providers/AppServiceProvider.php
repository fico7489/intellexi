<?php

namespace App\Providers;

use App\ESModule\Cdc\Consumer\Laravel\Maxwell\RedisSubscribe;
use App\ESModule\Cdc\Producer\Eloquent\EloquentListener;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->commands([
            RedisSubscribe::class,
        ]);

        app(EloquentListener::class)->listen();
    }
}
