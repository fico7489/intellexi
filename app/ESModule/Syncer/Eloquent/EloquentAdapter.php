<?php

namespace App\ESModule\Syncer\Eloquent;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use Illuminate\Database\Eloquent\Model;

class EloquentAdapter
{
    public function fetchModel(string $className, ChangedRowGroupedDto $changedRowGrouped): ?Model
    {
        // MAKE sure that newest model is fetched

        return $className::find($changedRowGrouped->getIdentifier());
    }
}
