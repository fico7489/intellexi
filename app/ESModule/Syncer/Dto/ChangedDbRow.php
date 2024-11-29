<?php

namespace ESModule\Syncer\Dto;

class ChangedDbRow
{
    final public const string TYPE_CREATE = 'create';
    final public const string TYPE_UPDATE = 'update';
    final public const string TYPE_DELETE = 'delete';

    public function __construct(
        private readonly string $table,
        private readonly string $identifier,
        private readonly string $type,
        private readonly array $changedFields = [],
    ) {
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
}
