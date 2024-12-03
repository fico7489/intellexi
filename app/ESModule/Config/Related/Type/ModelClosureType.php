<?php

namespace App\ESModule\Config\Related\Type;

use Closure;

class ModelClosureType
{
    public function __construct(
        private readonly Closure $closure,
    )
    {
    }

    public function getClosure(): \Closure
    {
        return $this->closure;
    }

}
