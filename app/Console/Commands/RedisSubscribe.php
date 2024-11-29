<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'redis:subscribe';

    public function handle()
    {
        Redis::subscribe(['maxwell'], function ($message) {
            dump($message);
        });
    }
}
