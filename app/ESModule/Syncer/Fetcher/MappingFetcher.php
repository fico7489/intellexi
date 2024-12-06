<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use Illuminate\Database\Eloquent\Model;

class MappingFetcher
{
    public function fetch(
        IndexDefinerModelInterface $index,
        Model $model,
        SyncItemDto $syncItemDto,
    ) {
        $mapping = [];

        // TODO decorate

        $mapping = $index->getMapping($mapping, $model);

        return $mapping;
    }
}
