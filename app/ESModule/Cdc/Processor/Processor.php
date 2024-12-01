<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Syncer\Handler;

readonly class Processor
{
    public function __construct(
        private Grouper $grouper,
        private Handler $syncer,
    ) {
    }

    public function process(array $payload): void
    {
        $this->syncer->handleRaw($payload);

        $dataGrouped = $this->grouper->group($payload);

        $this->syncer->handleGrouped($dataGrouped);
    }
}
