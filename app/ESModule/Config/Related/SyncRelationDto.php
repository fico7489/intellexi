<?php

namespace App\ESModule\Config\Related;

class SyncRelationDto
{
    public function __construct(
        private readonly string $className,
        private readonly string $relation,
        private readonly array $updatingFields,
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function getUpdatingFields(): array
    {
        return $this->updatingFields;
    }
}
