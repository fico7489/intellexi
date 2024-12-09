<?php

namespace App\ESModule\Syncer\Creator\Document\Dto;

class DocumentDto
{
    final public const string TYPE_UPSERT = 'upsert';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $indexName, // TODO index DTO
        private readonly mixed $identifierValue,
        private readonly array $data,
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

    public function getData(): array
    {
        return $this->data;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
