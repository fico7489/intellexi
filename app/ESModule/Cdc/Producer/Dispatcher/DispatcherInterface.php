<?php

namespace App\ESModule\Cdc\Producer\Dispatcher;

interface DispatcherInterface
{
    public function dispatch(array $data): void;
}
