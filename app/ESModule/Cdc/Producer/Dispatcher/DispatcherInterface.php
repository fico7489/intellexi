<?php

namespace App\ESModule\Cdc\Producer\Dispatcher;

/**
 * It dispatched received data to a custom place (redis pub/sub, redis lpush, rabbitmq, file, etc.).
 */
interface DispatcherInterface
{
    public function dispatch(array $data): void;
}
