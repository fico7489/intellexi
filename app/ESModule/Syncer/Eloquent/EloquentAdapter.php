<?php

namespace App\ESModule\Syncer\Eloquent;

use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncDto;
use Illuminate\Database\Eloquent\Model;

class EloquentAdapter
{
    public function fetchModel(string $className, SyncDto $syncDto): ?Model
    {
        // MAKE sure that newest model is fetched

        return $className::find($syncDto->getIdentifierValue());
    }
}
