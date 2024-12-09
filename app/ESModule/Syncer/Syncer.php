<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Creator\CdcSyncable\CdcSyncableCreator;
use App\ESModule\Syncer\Creator\Document\SyncableDocumentsCreator;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;

class Syncer
{
    public function __construct(
        private readonly CdcSyncableCreator $cdcSyncableCreator,
        private readonly SyncableDocumentsCreator $syncableDocumentsCreator,
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

        $documentDtos = $this->syncableDocumentsCreator->create($cdcSyncableDtos);
        dump('  Found documentDtos count='.count($documentDtos));

        $this->searchEngineDataClient->syncDocuments($documentDtos);
    }
}
