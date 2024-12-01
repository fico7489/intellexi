<?php

namespace App\ESModule\Cdc\Consumer;

use App\ESModule\Cdc\Converter\MaxwellConverter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisConsumer extends Command
{
    protected $signature = 'cdc:consumer:redis-subscribe {channel}';

    public function handle()
    {
        $channel = $this->argument('channel');

        while(true){
            $payload = Redis::rpop($channel);

            if(gettype($payload) === 'NULL'){
                continue;
            }else{
                dump($payload);

                $dto = app(MaxwellConverter::class)->convert($payload);

                dump($dto);
            }

            sleep(0.5);
        }
    }
}
