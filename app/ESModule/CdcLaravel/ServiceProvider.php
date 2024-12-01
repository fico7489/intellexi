<?php

namespace App\ESModule\CdcLaravel;

use App\ESModule\Cdc\Strategy\List\Algorithm;
use App\ESModule\Cdc\Strategy\List\Storage\RedisStorage;
use App\ESModule\Cdc\Strategy\List\Storage\Storage;
use App\ESModule\Cdc\Syncer\DumpHandler;
use App\ESModule\Cdc\Syncer\Handler;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
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
        $this->app->bind(Handler::class, DumpHandler::class);

        $this->app->when(Algorithm::class)->needs('$limit')->give(100);
        $this->app->when(Algorithm::class)->needs('$sleep')->give(5);

        $this->app->when(RedisStorage::class)->needs('$channel')->give('maxwell');
    }
}
