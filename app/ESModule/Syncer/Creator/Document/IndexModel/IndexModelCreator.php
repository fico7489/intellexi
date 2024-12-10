<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\IndexModel\Helper\ModelSourceFetcher;

class IndexModelCreator
{
    public function __construct(
        private readonly IndexModelFlattener $indexModelFlattener,
        private readonly IndexModelItemCreator $indexModelItemCreator,
        private readonly ModelSourceFetcher $modelSourceFetcher,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $cdcSyncableDtos
     */
    public function create(array $cdcSyncableDtos): array
    {
        $indexModelDtosGrouped = [];
        foreach ($cdcSyncableDtos as $cdcSyncableDto) {
            $indexNames = $cdcSyncableDto->getIndexNamesForSync();
            foreach ($indexNames as $indexName) {
                // detect $modelSource
                $modelSource = $this->modelSourceFetcher->fetch($cdcSyncableDto);

                $indexModelDtosGrouped = $this->indexModelItemCreator->create($indexModelDtosGrouped, $cdcSyncableDto, $modelSource, $indexName);
            }
        }

        $indexModelDtosFlattened = $this->indexModelFlattener->flatten($indexModelDtosGrouped);

        return $indexModelDtosFlattened;
    }
}
