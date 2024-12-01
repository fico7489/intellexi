<?php

namespace App\ESModule\Cdc\Syncer;

class SyncerDump
{
    // TODO send DTO
    public function sync($payload): void
    {
        dump($payload);
    }
}
