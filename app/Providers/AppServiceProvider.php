<?php

namespace App\Providers;

use App\ESModule\Cdc\Laravel\Consumer;
use App\ESModule\Cdc\Strategy\List\Storage\Adapter;
use App\ESModule\Cdc\Strategy\List\Storage\RedisAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->commands([
            Consumer::class,
        ]);

        // app(EloquentListener::class)->listen();

        $this->app->bind(Adapter::class, RedisAdapter::class);
    }
}
