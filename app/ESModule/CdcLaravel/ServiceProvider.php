<?php

namespace App\ESModule\CdcLaravel;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Converter\MaxwellConverter;
use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Cdc\Event\CdcPayloadsEvent;
use App\ESModule\Cdc\Storage\List\ListAlgorithm;
use App\ESModule\Cdc\Storage\List\ListStorage;
use App\ESModule\CdcStorageRedis\RedisListStorage;
use App\ESModule\Syncer\Syncer;
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
        $this->app->bind(ListStorage::class, RedisListStorage::class);
        $this->app->bind(EventDispatcherInterface::class, EventDispatcher::class);
        $this->app->bind(ConverterInterface::class, MaxwellConverter::class);

        // config
        $this->app->when(ListAlgorithm::class)->needs('$limit')->give(100);
        $this->app->when(ListAlgorithm::class)->needs('$sleep')->give(2);

        $this->app->when(RedisListStorage::class)->needs('$channel')->give('maxwell');
        $this->app->when(RedisListStorage::class)->needs('$options')->give(config('database.redis.default'));

        /*Event::listen(function (CdcPayloadsEvent $event) {
            dump('laravel event CdcPayloads:', $event);
        });*/

        Event::listen(function (CdcDtosEvent $event) {
            // dump('laravel event CdcChangedRowsGrouped:', $event->getCdcDtos());

            app(Syncer::class)->sync($event);
        });
    }
}
