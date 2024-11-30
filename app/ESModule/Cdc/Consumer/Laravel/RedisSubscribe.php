<?php

namespace App\ESModule\Cdc\Consumer\Laravel;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Converter\GeneralConverter;
use App\ESModule\Cdc\Converter\MaxwellConverter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'cdc:consumer:laravel:redis-subscribe {channel} {converter}';

    public function handle()
    {
        $channel = $this->argument('channel');
        $converter = $this->argument('converter');

        if($converter === 'maxwell'){
            $converter = app(MaxwellConverter::class);
        }else{
            $converter = app(GeneralConverter::class);
        }

        Redis::subscribe([$channel], function ($message) use ($converter) {
            dump($message);
            /** @var ConverterInterface $data */
            $data = $converter->convert($message);
            dump($data);
        });
    }
}
