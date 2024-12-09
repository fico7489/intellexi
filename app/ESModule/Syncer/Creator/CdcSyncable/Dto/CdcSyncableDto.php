<?php

namespace App\ESModule\Syncer\Creator\CdcSyncable\Dto;

class CdcSyncableDto
{
    final public const string TYPE_UPSERT = 'upsert';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $tableName,
        private readonly string $type,
        private array $data,
        private array $changedFields,
        private readonly mixed $identifierValue,
        private array $indexNames,
    ) {
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

    public function getIdentifierValue(): mixed
    {
        return $this->identifierValue;
    }

    public function getIndexNamesForSync(): array
    {
        return $this->indexNames;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function setChangedFields(array $changedFields): void
    {
        $this->changedFields = $changedFields;
    }

    public function setIndexNames(array $indexNames): void
    {
        $this->indexNames = $indexNames;
    }
}
