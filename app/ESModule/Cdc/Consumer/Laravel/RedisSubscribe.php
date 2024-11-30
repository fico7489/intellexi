<?php

namespace App\ESModule\Cdc\Consumer\Laravel;

use App\ESModule\Cdc\Converter\ConverterInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'cdc:consumer:laravel:redis-subscribe {channel}';

    public function handle()
    {
        $channel = $this->argument('channel');

        Redis::subscribe([$channel], function ($message) {
            dump($message);
            $data = app(ConverterInterface::class)->convert($message);
            dump($data);
        });
    }
}
