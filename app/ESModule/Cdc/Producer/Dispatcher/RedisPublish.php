<?php

namespace App\ESModule\Cdc\Producer\Dispatcher;

use Illuminate\Support\Facades\Redis;

readonly class RedisPublish implements DispatcherInterface
{
    public function __construct(
        private string $channel,
    ) {
    }

    public function dispatch(array $data): void
    {
        Redis::publish($this->channel, json_encode($data));
    }
}
