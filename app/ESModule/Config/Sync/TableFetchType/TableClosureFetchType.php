<?php

namespace App\ESModule\Config\Sync\TableFetchType;

class TableClosureFetchType
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
