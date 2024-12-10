<?php

namespace App\ESModule\Syncer\Creator\Document\Dto;

class IndexModelDto
{
    public function __construct(
        private readonly string $indexName,
        private readonly mixed $identifierValue,
        private readonly string $type,
        private readonly object $model,
    ) {
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function getIdentifierValue(): mixed
    {
        return $this->identifierValue;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getModel(): object
    {
        return $this->model;
    }
}
