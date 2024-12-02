<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;

class Syncer
{
    public function sync(array $changedRowsGrouped)
    {
        foreach ($changedRowsGrouped as $changedRowGrouped) {
            /* @var ChangedRowGroupedDto $changedRowGrouped */
        }
    }

    private function detectModels()
    {
    }
}
