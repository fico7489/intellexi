<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class MappingFetcher
{
    public function fetch(IndexDto $indexDto, object $model, CdcSyncableDto $syncItemDto): array
    {
        $mapping = [];

        // TODO decorate

        $mapping = $indexDto->getMapping($mapping, $model);

        return $mapping;
    }
}
