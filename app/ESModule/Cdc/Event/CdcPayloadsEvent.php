<?php

namespace App\ESModule\Cdc\Event;

readonly class CdcPayloadsEvent
{
    public function __construct(
        private array $cdcPayloads,
    ) {
    }

    public function getCdcPayloads(): array
    {
        return $this->cdcPayloads;
    }
}
