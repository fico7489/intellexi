<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Syncer\Creator\SyncableItem\Dto\SyncableItemDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class MappingFetcher
{
    public function fetch(IndexDto $indexDto, object $model, SyncableItemDto $syncItemDto): array
    {
        $mapping = [];

        // TODO decorate

        $mapping = $indexDto->getMapping($mapping, $model);

        return $mapping;
    }
}
