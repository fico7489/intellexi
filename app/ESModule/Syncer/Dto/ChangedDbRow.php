<?php

namespace App\ESModule\Syncer\Dto;

class ChangedDbRow
{
    final public const string TYPE_CREATE = 'create';
    final public const string TYPE_UPDATE = 'update';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $database,
        private readonly string $table,
        private readonly string $type,
        private readonly string $identifier,// TODO do we need
        private readonly array $changedFields,
        private readonly array $data,
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

    public function getIdentifier(): string
    {
        return $this->identifier;
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
}
