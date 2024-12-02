<?php

namespace App\ESModule\Cdc\Storage\List;

interface ListStorage
{
    public function popFromList(int $limit): ?array;
}
