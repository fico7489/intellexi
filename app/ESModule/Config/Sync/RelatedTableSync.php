<?php

namespace App\ESModule\Config\Sync;

use App\ESModule\Config\Sync\TableFetchType\TableClosureFetchType;

class RelatedTableSync
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
