<?php

namespace App\ESModule\Cdc\Producer\Dispatcher;

use Illuminate\Support\Facades\Redis;

class RedisLPush implements DispatcherInterface
{
    public function dispatch(array $data): void
    {
        Redis::lpush(random_int(1, 10000), json_encode($data));
    }
}
