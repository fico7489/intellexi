<?php

namespace App\ESModule\Config\SyncType;

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
