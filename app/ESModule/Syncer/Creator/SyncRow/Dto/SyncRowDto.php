<?php

namespace App\ESModule\Syncer\Creator\SyncRow\Dto;

class SyncRowDto
{
    final public const string TYPE_UPSERT = 'upsert';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $database,
        private readonly string $table,
        private readonly string $type,
        private array $data,
        private array $changedFields,
        private readonly mixed $identifier,
    ) {
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getChangedFields(): array
    {
        return $this->changedFields;
    }

    public function getIdentifier(): mixed
    {
        return $this->identifier;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function setChangedFields(array $changedFields): void
    {
        $this->changedFields = $changedFields;
    }
}
