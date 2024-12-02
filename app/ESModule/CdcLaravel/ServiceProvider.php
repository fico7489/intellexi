<?php

namespace App\ESModule\CdcLaravel;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Converter\MaxwellConverter;
use App\ESModule\Cdc\Event\CdcChangedRows;
use App\ESModule\Cdc\Event\CdcPayloads;
use App\ESModule\Cdc\Storage\List\Algorithm;
use App\ESModule\Cdc\Storage\List\Storage;
use App\ESModule\CdcStorageRedis\RedisStorage;
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

        // bind
        $this->app->bind(Storage::class, RedisStorage::class);
        $this->app->bind(EventDispatcherInterface::class, EventDispatcher::class);
        $this->app->bind(ConverterInterface::class, MaxwellConverter::class);

        // config
        $this->app->when(Algorithm::class)->needs('$limit')->give(100);
        $this->app->when(Algorithm::class)->needs('$sleep')->give(15);

        $this->app->when(RedisStorage::class)->needs('$channel')->give('maxwell');
        $this->app->when(RedisStorage::class)->needs('$options')->give(config('database.redis.default'));

        Event::listen(function (CdcPayloads $event) {
            dump('laravel event listener raw', $event->getPayloads());
        });

        Event::listen(function (CdcChangedRows $event) {
            dump('laravel event listener grouped', $event->getPayload());
        });
    }
}
