<?php

namespace App\Providers;

use App\ESModule\CdcConsumerLaravel\Maxwell\Redis\RedisSubscribe;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->commands([
            RedisSubscribe::class
        ]);
    }
}
