<?php

namespace App\ESModule\Config\RelatedSync;

use App\ESModule\Config\RelatedSync\ModelFetchType\ModelClosureFetchType;
use App\ESModule\Config\RelatedSync\ModelFetchType\ModelRelationFetchType;

class ModelRelatedSync
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
