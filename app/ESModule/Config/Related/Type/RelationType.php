<?php

namespace App\ESModule\Config\Related\Type;

class RelationType
{
    public function __construct(
        private readonly string $relation,
    )
    {
    }

    public function getRelation(): string
    {
        return $this->relation;
    }
}
