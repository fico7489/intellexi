<?php

namespace App\ESModule\CdcConsumerLaravel\Maxwell\Redis;

use App\ESModule\Syncer\Adapter\MaxwellAdapter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'cdc-consumer-laravel:redis-subscribe';

    public function handle()
    {
        Redis::subscribe(['maxwell'], function ($message) {
            dump($message);
            $data = app(MaxwellAdapter::class)->convert($message);
            dump($data);
        });
    }
}
