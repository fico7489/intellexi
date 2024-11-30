<?php

namespace App\ESModule\Cdc\Producer\Dispatcher;

use Illuminate\Support\Facades\Redis;

class RedisLPush implements DispatcherInterface
{
    public function dispatch(array $data): void
    {
        Redis::lpush('queues:high', json_encode($data));

        dump(11);
    }
}
