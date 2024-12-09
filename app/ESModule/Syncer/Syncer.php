<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator;
use App\ESModule\Syncer\Creator\SyncableDocument\SyncableDocumentsCreator;
use App\ESModule\Syncer\Creator\SyncableItem\SyncableItemsCreator;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;

class Syncer
{
    public function __construct(
        private readonly SyncableItemsCreator $syncableItemsCreator,
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
        dump('count $cdcDtos='.count($cdcRawDtos));

        $cdcSyncableDtos = $this->syncableItemsCreator->create($cdcRawDtos);
        dump('count $cdcSyncableDtos='.count($cdcSyncableDtos));

        $syncableDocumentDtos = $this->syncableDocumentsCreator->create($cdcSyncableDtos);
        dump('count $syncableDocumentDtos='.count($syncableDocumentDtos));

        $documents = $this->documentsCreator->create($syncableDocumentDtos);
        dump('count $documents='.count($documents));

        $this->searchEngineDataClient->syncDocuments($documents);
    }
}
