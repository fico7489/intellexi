<?php

namespace App\ESModule\Cdc\Event;

readonly class CdcGrouped
{
    public function __construct(
        private array $payload,
    ) {
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
