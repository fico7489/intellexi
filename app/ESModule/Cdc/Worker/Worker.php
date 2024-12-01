<?php

namespace App\ESModule\Cdc\Worker;

use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Storage\RedisStorage;

class Worker
{
    public function work()
    {
        /** @var RedisStorage $storage */
        $storage = app(RedisStorage::class);

        //TODO
        $time = time();

        while (true) {
            $payload = $storage->readCdc();

            if (null !== $payload) {
                $storage->store($payload);
            }

            //TODO
            sleep(1);

            $timeCurrent = time();
            $seconds = $timeCurrent - $time;

            if ($seconds > 5) {
                $time = $timeCurrent;

                $data = $storage->readAndDelete();

                app(Grouper::class)->group($data);
            }
        }
    }
}
