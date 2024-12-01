<?php

namespace App\ESModule\Cdc\Syncer;

interface Syncer
{
    public function sync($payload): void;
}
