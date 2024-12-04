<?php

namespace App\ESModule\Config\RelatedSync\FetchType;

class ClosureFetchType
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
