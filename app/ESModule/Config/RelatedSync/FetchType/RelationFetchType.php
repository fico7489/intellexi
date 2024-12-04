<?php

namespace App\ESModule\Config\RelatedSync\FetchType;

class RelationFetchType
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
