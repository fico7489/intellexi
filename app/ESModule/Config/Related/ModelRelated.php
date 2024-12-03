<?php

namespace App\ESModule\Config\Related;

class ModelRelated
{
    public function __construct(
        private readonly string $className,
        private readonly ?array $updatingFields,
        private readonly mixed $fetchType, // TODO
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

    public function getFetchType(): mixed
    {
        return $this->fetchType;
    }
}
