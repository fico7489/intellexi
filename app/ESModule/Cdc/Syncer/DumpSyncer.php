<?php

namespace App\ESModule\Cdc\Syncer;

class DumpSyncer implements Syncer
{
    // TODO send DTO
    public function sync($payload): void
    {
        dump('syncer', $payload);
    }
}
