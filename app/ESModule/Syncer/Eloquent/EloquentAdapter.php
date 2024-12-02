<?php

namespace App\ESModule\Syncer\Eloquent;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use Illuminate\Database\Eloquent\Model;

class EloquentAdapter
{
    public function fetchModel(string $className, ChangedRowGroupedDto $changedRowGrouped): ?Model
    {
        return $className::find($changedRowGrouped->getIdentifier());
    }
}
