<?php

namespace App\ESModule\Cdc\Strategy\List\Storage;

interface Adapter
{
    public function readCdc(int $limit): ?array;
}
