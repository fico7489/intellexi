<?php

namespace App\ESModule\Cdc\Event;

readonly class CdcChangedRowsGrouped
{
    public function __construct(
        private array $changedRowsGrouped,
    ) {
    }

    public function getChangedRowsGrouped(): array
    {
        return $this->changedRowsGrouped;
    }
}
