<?php

namespace App\ESModule\Cdc\Consumer;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisConsumer extends Command
{
    protected $signature = 'cdc:consumer:redis-subscribe {channel}';

    public function handle()
    {
        $channel = $this->argument('channel');

        while(true){
            $data = Redis::rpop($channel);

            if(gettype($data) === 'NULL'){
                continue;
            }else{
                dump($data);
            }

            sleep(0.5);
        }
    }
}
