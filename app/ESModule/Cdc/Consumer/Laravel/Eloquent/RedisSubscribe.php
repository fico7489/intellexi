<?php

namespace App\ESModule\Cdc\Consumer\Laravel\Eloquent;

use App\ESModule\Syncer\Adapter\MaxwellAdapter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'cdc:consumer:laravel:eloquent:redis-subscribe';

    public function handle()
    {
        Redis::subscribe(['eloquent'], function ($message) {
            dump($message);
            $data = app(MaxwellAdapter::class)->convert($message);
            dump($data);
        });
    }
}
