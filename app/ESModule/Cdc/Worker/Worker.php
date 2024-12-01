<?php

namespace App\ESModule\Cdc\Worker;

use App\ESModule\Cdc\Grouper\Grouper;
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

                dump($payload);
            }

            sleep(1);

            $timeCurrent = time();
            $seconds = $timeCurrent - $time;

            if ($seconds > 15) {
                $time = $timeCurrent;

                $data = Redis::smembers('DATA');
                Redis::del(['DATA']);

                app(Grouper::class)->group($data);
            }
        }
    }
}
