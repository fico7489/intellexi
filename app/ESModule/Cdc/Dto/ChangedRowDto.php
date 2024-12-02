<?php

namespace App\ESModule\Cdc\Dto;

class ChangedRowDto
{
    final public const string TYPE_INSERT = 'insert';
    final public const string TYPE_UPDATE = 'update';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private string $database,
        private string $table,
        private string $type,
        private array $changedFields,
        private array $data,
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

    public function getChangedFields(): array
    {
        return $this->changedFields;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setChangedFields(array $changedFields): void
    {
        $this->changedFields = $changedFields;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
