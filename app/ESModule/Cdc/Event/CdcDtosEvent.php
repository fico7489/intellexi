<?php

namespace App\ESModule\Cdc\Event;

use App\ESModule\Cdc\Dto\CdcDto;

readonly class CdcDtosEvent
{
    public function __construct(
        private array $cdcDtos,
    ) {
    }

    /**
     * @return array<CdcDto>
     */
    public function getCdcDtos(): array
    {
        return $this->cdcDtos;
    }
}
