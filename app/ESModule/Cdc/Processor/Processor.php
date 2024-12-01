<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Syncer\Syncer;

readonly class Processor
{
    public function __construct(
        private Grouper $grouper,
        private Syncer  $syncer,
    )
    {
    }

    // TODO send DTO
    public function process($payload): void
    {
        $dataGrouped = $this->grouper->group($payload);

        $this->syncer->sync($dataGrouped);
    }
}
