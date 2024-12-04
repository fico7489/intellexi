<?php

namespace App\ESModule\Config\RelatedSync;

use App\ESModule\Config\RelatedSync\FetchType\ClosureFetchType;
use App\ESModule\Config\RelatedSync\FetchType\RelationFetchType;

class ModelRelatedSync
{
    public function __construct(
        private readonly string $className,
        private readonly ?array $updatingFields,
        private readonly RelationFetchType|ClosureFetchType $fetchType,
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

    public function getFetchType(): ClosureFetchType|RelationFetchType
    {
        return $this->fetchType;
    }
}
