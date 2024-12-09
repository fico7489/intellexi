<?php

namespace App\ESModule\Syncer\Creator\SyncableDocument\Dto;

class SyncableDocumentDto
{
    public function __construct(
        private readonly string $indexName,
        private readonly mixed $identifierValue,
        private readonly string $type,
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
}
