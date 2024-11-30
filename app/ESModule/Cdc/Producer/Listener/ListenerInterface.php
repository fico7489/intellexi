<?php

namespace App\ESModule\Cdc\Producer\Listener;

interface ListenerInterface
{
    public function listen(): void;
}
