<?php

namespace App\ESModule\Cdc\Producer\Dispatcher;

use Illuminate\Support\Facades\Redis;

class RedisPublish implements DispatcherInterface
{
    public function dispatch(array $data): void
    {
        Redis::publish('eloquent', json_encode($data));
    }
}
