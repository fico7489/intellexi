<?php

namespace App\ESModule\Config\Related;

class SyncRelationDto
{
    public function __construct(
        private readonly string $className,
        private readonly array  $updatingFields,
        private readonly mixed  $detection, //TODO
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getUpdatingFields(): array
    {
        return $this->updatingFields;
    }

    public function getDetection(): mixed
    {
        return $this->detection;
    }
}
