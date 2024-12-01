<?php

namespace App\ESModule\CdcLaravel;

use App\ESModule\Cdc\Event\CdcGrouped;
use App\ESModule\Cdc\Event\CdcRaw;
use App\ESModule\Cdc\Strategy\List\Algorithm;
use App\ESModule\Cdc\Strategy\List\Storage\RedisStorage;
use App\ESModule\Cdc\Strategy\List\Storage\Storage;
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
        $this->app->bind(EventDispatcherInterface::class, EventDispatcher::class);

        $this->app->when(Algorithm::class)->needs('$limit')->give(100);
        $this->app->when(Algorithm::class)->needs('$sleep')->give(5);

        $this->app->when(RedisStorage::class)->needs('$channel')->give('maxwell');

        Event::listen(function (CdcRaw $event) {
            dump('laravel event listener raw', $event->getPayload());
        });

        Event::listen(function (CdcGrouped $event) {
            dump('laravel event listener grouped', $event->getPayload());
        });
    }
}
