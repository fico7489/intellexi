<?php

namespace App\ESModule\Cdc\Event;

readonly class CdcRaw
{
    public function __construct(
        private array $payload,
    )
    {
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
