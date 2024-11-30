<?php

namespace App\Providers;

use App\ESModule\Cdc\Consumer\Laravel\Maxwell\RedisSubscribe;
use App\ESModule\Cdc\Producer\Dispatcher\DispatcherInterface;
use App\ESModule\Cdc\Producer\Dispatcher\RedisPublish;
use App\ESModule\Cdc\Producer\Listener\EloquentListener;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DispatcherInterface::class, function ($app) {
            return new RedisPublish();
        });
    }

    public function boot(): void
    {
        $this->commands([
            RedisSubscribe::class,
        ]);

        app(EloquentListener::class)->listen();
    }
}
