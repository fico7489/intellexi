<?php

namespace App\ESModule\Cdc\Syncer;

interface Handler
{
    public function handleRaw(array $payload): void;

    public function handleGrouped(array $payload): void;
}
