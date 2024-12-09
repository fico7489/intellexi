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
    public function sync(array $cdcDtos): void
    {
        dump('count $cdcDtos='.count($cdcDtos));
        $syncableItemDtos = $this->syncableItemsCreator->create($cdcDtos);

        dump('count $syncableItemDtos='.count($syncableItemDtos));
        $syncableDocumentDtos = $this->syncableDocumentsCreator->create($syncableItemDtos);

        dump('count $syncableDocumentDtos='.count($syncableDocumentDtos));
        $documents = $this->documentsCreator->create($syncableDocumentDtos);

        $this->searchEngineDataClient->syncDocuments($documents);
    }
}
