<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;

class MappingFetcher
{
    public function fetch(IndexDefinerModelInterface $index, object $model, SyncItemDto $syncItemDto)
    {
        $mapping = [];

        // TODO decorate

        $mapping = $index->getMapping($mapping, $model);

        return $mapping;
    }
}
