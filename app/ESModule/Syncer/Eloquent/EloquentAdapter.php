<?php

namespace App\ESModule\Syncer\Eloquent;

use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;
use Illuminate\Database\Eloquent\Model;

class EloquentAdapter
{
    public function fetchModel(string $className, SyncRowDto $syncRowDto): ?Model
    {
        // MAKE sure that newest model is fetched

        return $className::find($syncRowDto->getIdentifierValue());
    }
}
