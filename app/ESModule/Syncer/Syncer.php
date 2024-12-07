<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Event\CdcDtosEvent;
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

    public function sync(CdcDtosEvent $event): void
    {
        $cdcDtos = $event->getCdcDtos();

        $syncItemDtos = $this->syncItemCreator->create($cdcDtos);

        $documentDtos = $this->documentsCreator->create($syncItemDtos);

        $this->searchEngineSyncer->syncDocuments($documentDtos);
    }
}
