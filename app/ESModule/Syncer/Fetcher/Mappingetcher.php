<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use Illuminate\Database\Eloquent\Model;

class Mappingetcher
{
    public function fetch(
        IndexDefinerModelInterface $index,
        Model $model,
        SyncDto $syncDto,
    ) {
        $mapping = [];

        // TODO decorate

        $mapping = $index->getMapping($mapping, $model);

        return $mapping;
    }
}
