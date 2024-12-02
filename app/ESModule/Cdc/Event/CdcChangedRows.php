<?php

namespace App\ESModule\Cdc\Event;

readonly class CdcChangedRows
{
    public function __construct(
        private array $changedRows,
    ) {
    }

    public function getChangedRows(): array
    {
        return $this->changedRows;
    }
}
