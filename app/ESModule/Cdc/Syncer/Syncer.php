<?php

namespace App\ESModule\Cdc\Syncer;

use App\ESModule\Cdc\Grouper\Grouper;

class Syncer
{
    // TODO send DTO
    public function sync($payload): void
    {
        /** @var Grouper $grouper */
        $grouper = app(Grouper::class);

        $dataGrouped = $grouper->group($payload);

        dump($payload);
    }
}
