<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator;
use App\ESModule\Syncer\Creator\SyncItem\SyncItemCreator;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;

class Syncer
{
    public function __construct(
        private readonly SyncItemCreator $syncItemCreator,
        private readonly DocumentsCreator $documentsCreator,
        private readonly SearchEngineDataClient $searchEngineSyncer,// TODO by interface
    ) {
    }

    /**
     * @params array<CdcDto>
     */
    public function sync(array $cdcDtos): void
    {
        dump('count $cdcDtos='.count($cdcDtos));
        $syncItemDtos = $this->syncItemCreator->create($cdcDtos);

        dump('count $syncItemDtos='.count($syncItemDtos));
        $documentDtos = $this->documentsCreator->create($syncItemDtos);

        dump('count $documentDtos='.count($documentDtos));
        $this->searchEngineSyncer->syncDocuments($documentDtos);
    }
}
