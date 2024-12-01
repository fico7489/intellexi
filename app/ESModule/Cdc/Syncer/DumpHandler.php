<?php

namespace App\ESModule\Cdc\Syncer;

class DumpHandler implements Handler
{
    // TODO send DTO
    public function handleGrouped(array $payload): void
    {
        dump('syncer', $payload);
    }

    public function handleRaw(array $payload): void
    {
        // TODO: Implement handleRaw() method.
    }
}
