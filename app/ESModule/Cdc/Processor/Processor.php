<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Syncer\Syncer;

class Processor
{
    // TODO send DTO
    public function process($payload): void
    {
        /** @var Grouper $grouper */
        $grouper = app(Grouper::class);

        /** @var Syncer $syncer */
        $syncer = app(Syncer::class);

        $dataGrouped = $grouper->group($payload);

        $syncer->sync($dataGrouped);
    }
}
