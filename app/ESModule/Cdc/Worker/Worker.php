<?php

namespace App\ESModule\Cdc\Worker;

use Illuminate\Support\Facades\Redis;

class Worker
{
    public function work($channel)
    {
        $time = time();

        while (true) {
            $payload = Redis::rpop($channel);

            if ('NULL' !== gettype($payload)) {
                Redis::sadd('DATA', $payload);
            }

            sleep(1);

            $timeCurrent = time();
            $seconds = $timeCurrent -$time;

           if ($seconds > 5) {
               $time = $timeCurrent;

               $data = Redis::smembers('DATA');
               Redis::del(['DATA']);

               dump($data);
           }
        }
    }
}
