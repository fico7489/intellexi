<?php

namespace App\ESModule\Cdc\Event;

readonly class CdcRaw
{
    public function __construct(
        private array $payloads,
    ) {
    }

    public function getPayloads(): array
    {
        return $this->payloads;
    }
}
