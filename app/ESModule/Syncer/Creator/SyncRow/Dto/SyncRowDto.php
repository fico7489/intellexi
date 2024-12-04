<?php

namespace App\ESModule\Syncer\Creator\SyncRow\Dto;

class SyncRowDto
{
    final public const string TYPE_UPSERT = 'upsert';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $databaseName,
        private readonly string $tableName,
        private readonly string $type,
        private array $data,
        private array $changedFields,
        private readonly string|array $identifierValue,
    ) {
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
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

    public function getIdentifierValue(): array|string
    {
        return $this->identifierValue;
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
