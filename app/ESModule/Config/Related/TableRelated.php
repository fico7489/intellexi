<?php

namespace App\ESModule\Config\Related;

class TableRelated
{
    public function __construct(
        private readonly string $tableName,
        private readonly ?array $updatingFields,
        private readonly mixed $fetchType, // TODO
    ) {
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getUpdatingFields(): ?array
    {
        return $this->updatingFields;
    }

    public function getFetchType(): mixed
    {
        return $this->fetchType;
    }
}
