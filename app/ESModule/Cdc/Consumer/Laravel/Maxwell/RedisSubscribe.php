<?php

namespace App\ESModule\Cdc\Consumer\Laravel\Maxwell;

use App\ESModule\Syncer\Adapter\MaxwellAdapter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'cdc:consumer:laravel:maxwell:redis-subscribe';

    public function handle()
    {
        Redis::subscribe(['maxwell'], function ($message) {
            dump($message);
            $data = app(MaxwellAdapter::class)->convert($message);
            dump($data);
        });
    }
}
