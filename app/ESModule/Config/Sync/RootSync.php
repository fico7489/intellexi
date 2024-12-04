<?php

namespace App\ESModule\Config\Sync;

class RootSync
{
    public function __construct(
        private readonly ?array $updatingFields,
    ) {
    }

    public function getUpdatingFields(): ?array
    {
        return $this->updatingFields;
    }
}
