<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Event\CdcChangedRowsGrouped;

class Syncer
{
    public function sync(CdcChangedRowsGrouped $event)
    {
        $changedRowsGrouped = $event->getChangedRowsGrouped();

        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $changedRowGrouped) {
                /* @var ChangedRowGroupedDto $changedRowGrouped */

                dump('SYNCER:',
                    $table,
                    $identifier,
                    $changedRowGrouped,
                );
            }
        }
    }

    private function detectModels()
    {
    }
}
