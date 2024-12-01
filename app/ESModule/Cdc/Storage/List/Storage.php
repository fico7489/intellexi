<?php

namespace App\ESModule\Cdc\Storage\List;

interface Storage
{
    public function pop(int $limit): ?array;
}
