<?php

namespace App\ESModule\Config\SyncType;

use App\ESModule\Config\SyncType\ModelFetchType\ModelClosureFetchType;
use App\ESModule\Config\SyncType\ModelFetchType\ModelRelationFetchType;

class RelatedModelSync
{
    public function __construct(
        private readonly string $className,
        private readonly ?array $updatingFields,
        private readonly ModelRelationFetchType|ModelClosureFetchType $fetchType,
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getUpdatingFields(): ?array
    {
        return $this->updatingFields;
    }

    public function getFetchType(): ModelClosureFetchType|ModelRelationFetchType
    {
        return $this->fetchType;
    }
}
