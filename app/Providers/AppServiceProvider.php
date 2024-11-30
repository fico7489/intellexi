<?php

namespace App\Providers;

use App\ESModule\Cdc\Consumer\Laravel\RedisSubscribe;
use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Converter\GeneralConverter;
use App\ESModule\Cdc\Converter\MaxwellConverter;
use App\ESModule\Cdc\Producer\Dispatcher\DispatcherInterface;
use App\ESModule\Cdc\Producer\Dispatcher\RedisPublish;
use App\ESModule\Cdc\Producer\Listener\EloquentListener;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /* CDC */
        $this->app->bind(DispatcherInterface::class, function ($app) {
            return new RedisPublish('eloquent');
        });
        /*$this->app->bind(ConverterInterface::class, function ($app) {
            return new MaxwellConverter();
        });*/
        $this->app->bind(ConverterInterface::class, function ($app) {
            return new GeneralConverter();
        });
        /* CDC */


    }

    public function boot(): void
    {
        $this->commands([
            RedisSubscribe::class,
        ]);

        app(EloquentListener::class)->listen();
    }
}
