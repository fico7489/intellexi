<?php

namespace App\ESModule\Cdc\Strategy\List;

interface Adapter
{
    public function readCdc(int $limit): ?array;
}
