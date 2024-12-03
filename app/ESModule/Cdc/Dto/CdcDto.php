<?php

namespace App\ESModule\Cdc\Dto;

class CdcDto
{
    final public const string TYPE_INSERT = 'insert';
    final public const string TYPE_UPDATE = 'update';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $databaseName,
        private readonly string $tableName,
        private readonly string $type,
        private readonly array $data,
        private readonly array $changedFields,
        private readonly array $additional,
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

    public function getAdditional(): array
    {
        return $this->additional;
    }
}
