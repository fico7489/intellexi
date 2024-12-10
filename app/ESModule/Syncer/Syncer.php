<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Creator\CdcSyncable\CdcSyncableCreator;
use App\ESModule\Syncer\Creator\Document\DocumentCreator;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;

class Syncer
{
    public function __construct(
        private readonly CdcSyncableCreator $cdcSyncableCreator,
        private readonly DocumentCreator $documentCreator,
        private readonly SearchEngineDataClient $searchEngineDataClient,// TODO by interface
    ) {
    }

    /**
     * @params array<CdcDto>
     */
    public function sync(array $cdcRawDtos): void
    {
        dump('Received cdcRaw count='.count($cdcRawDtos));

        $cdcSyncableDtos = $this->cdcSyncableCreator->create($cdcRawDtos);
        dump('  Found cdcSyncable count='.count($cdcSyncableDtos));

        $indexNamesForSyncCount = $this->fetchIndexNamesForSyncCount($cdcSyncableDtos);
        dump('  Found indexes for sync in cdcSyncable count='.$indexNamesForSyncCount);

        $documentDtos = $this->documentCreator->create($cdcSyncableDtos);
        dump('  Calculated documentDtos count='.count($documentDtos));

        $this->searchEngineDataClient->syncDocuments($documentDtos);
    }

    private function fetchIndexNamesForSyncCount(array $cdcSyncableDtos): int
    {
        $indexNamesForSyncCount = 0;
        foreach ($cdcSyncableDtos as $cdcSyncableDto) {
            $indexNamesForSyncCount += count($cdcSyncableDto->getIndexNamesForSync());
        }

        return $indexNamesForSyncCount;
    }
}
