<?php

namespace App\ESModule\Config\RelatedSync;

use App\ESModule\Config\RelatedSync\TableFetchType\TableClosureFetchType;

class TableRelatedSync
{
    public function __construct(
        private readonly string $tableName,
        private readonly ?array $updatingFields,
        private readonly TableClosureFetchType $fetchType,
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
