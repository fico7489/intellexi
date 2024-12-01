<?php

namespace App\ESModule\Cdc\Strategy\List\Storage;

interface Storage
{
    public function lmpop(int $limit): ?array;
}
