<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Creator\CdcSyncable\CdcSyncableCreator;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator;
use App\ESModule\Syncer\Creator\SyncableDocument\SyncableDocumentsCreator;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;

class Syncer
{
    public function __construct(
        private readonly CdcSyncableCreator $cdcSyncableCreator,
        private readonly SyncableDocumentsCreator $syncableDocumentsCreator,
        private readonly DocumentsCreator $documentsCreator,
        private readonly SearchEngineDataClient $searchEngineDataClient,// TODO by interface
    ) {
    }

    /**
     * @params array<CdcDto>
     */
    public function sync(array $cdcRawDtos): void
    {
        dump('Received $cdcRawDtos count='.count($cdcRawDtos));

        $cdcSyncableDtos = $this->cdcSyncableCreator->create($cdcRawDtos);
        dump('  Found $cdcSyncableDtos count='.count($cdcSyncableDtos));

        $syncableDocumentDtos = $this->syncableDocumentsCreator->create($cdcSyncableDtos);
        dump('    -> count $syncableDocumentDtos='.count($syncableDocumentDtos));

        $documents = $this->documentsCreator->create($syncableDocumentDtos);
        dump('  Calculated $documents count='.count($documents));

        $this->searchEngineDataClient->syncDocuments($documents);
    }
}
