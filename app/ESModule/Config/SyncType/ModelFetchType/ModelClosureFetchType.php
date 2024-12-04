<?php

namespace App\ESModule\Config\SyncType\ModelFetchType;

class ModelClosureFetchType
{
    public function __construct(
        private readonly \Closure $closure,
    ) {
    }

    public function getClosure(): \Closure
    {
        return $this->closure;
    }
}
