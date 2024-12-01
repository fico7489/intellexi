<?php

namespace App\ESModule\Cdc\Strategy\List\Storage;

interface Storage
{
    public function readCdc(int $limit): ?array;
}
