<?php

namespace App\ESModule\CdcLaravel;

use App\ESModule\Cdc\Event\CdcRaw;
use App\ESModule\Cdc\Strategy\List\Algorithm;
use App\ESModule\Cdc\Strategy\List\Storage\RedisStorage;
use App\ESModule\Cdc\Strategy\List\Storage\Storage;
use App\ESModule\Cdc\Syncer\DumpHandler;
use App\ESModule\Cdc\Syncer\Handler;
use Illuminate\Support\Facades\Event;
use Psr\EventDispatcher\EventDispatcherInterface;

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

        $this->app->bind(Storage::class, RedisStorage::class);
        $this->app->bind(Handler::class, DumpHandler::class);
        $this->app->bind(EventDispatcherInterface::class, EventDispatcher::class);


        $this->app->when(Algorithm::class)->needs('$limit')->give(100);
        $this->app->when(Algorithm::class)->needs('$sleep')->give(5);

        $this->app->when(RedisStorage::class)->needs('$channel')->give('maxwell');

        Event::listen(function (CdcRaw $event) {
            dump('laravel event listener', $event->getPayload());
        });
    }
}
