<?php

namespace App\ESModule\Config\Related\FetchType;

class RelationFetch
{
    public function __construct(
        private readonly string $relation,
    ) {
    }

    public function getRelation(): string
    {
        return $this->relation;
    }
}
