<?php

namespace App\ESModule\Config\RelatedSync\ModelFetchType;

class ModelRelationFetchType
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
