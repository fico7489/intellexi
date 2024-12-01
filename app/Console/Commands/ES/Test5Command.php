<?php

namespace App\Console\Commands\ES;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class Test5Command extends Command
{
    protected $signature = 'test5';

    public function handle()
    {
        while (true) {
            $data = Redis::rpop('queues:high');

            if ('NULL' === gettype($data)) {
                continue;
            } else {
                dump($data);
            }

            sleep(1);
        }
    }
}
