<?php

namespace App\ESModule;

use App\ES\Connection\DefaultConnection;
use App\ES\Index\ApplicationIndex;
use App\ES\Index\UserIndex;
use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Converter\MaxwellConverter;
use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Cdc\Storage\List\ListAlgorithm;
use App\ESModule\Cdc\Storage\List\ListStorage;
use App\ESModule\CdcLaravel\Consumer;
use App\ESModule\CdcLaravel\EventDispatcher;
use App\ESModule\CdcStorageRedis\RedisListStorage;
use App\ESModule\Syncer\Provider\ConfigProvider;
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
        $this->app->when(ListAlgorithm::class)->needs('$sleep')->give(0);

        $this->app->when(RedisListStorage::class)->needs('$channel')->give('maxwell');
        $this->app->when(RedisListStorage::class)->needs('$options')->give(config('database.redis.default'));

        // TODO
        $this->app->when(ConfigProvider::class)->needs('$configConnection')->give(app(DefaultConnection::class));

        $this->app->when(ConfigProvider::class)->needs('$configIndexes')->give([
            // TODO
            app(ApplicationIndex::class),
            app(UserIndex::class),
        ]);

        /*Event::listen(function (CdcPayloadsEvent $event) {
            dump('laravel event CdcPayloads:', $event);
        });*/

        Event::listen(function (CdcDtosEvent $event) {
            // dump('CDC Event:', $event->getCdcDtos());

            app(Syncer::class)->sync($event->getCdcDtos());
        });
    }
}
