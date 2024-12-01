<?php

namespace App\Providers;

use App\ESModule\Cdc\Laravel\Consumer;
use App\ESModule\Cdc\Strategy\List\Algorithm;
use App\ESModule\Cdc\Strategy\List\Storage\Storage;
use App\ESModule\Cdc\Strategy\List\Storage\RedisStorage;
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

        $this->app->bind(Storage::class, RedisStorage::class);

        $this->app->when(Algorithm::class)->needs('$limit')->give(100);
        $this->app->when(Algorithm::class)->needs('$sleep')->give(5);
    }
}
