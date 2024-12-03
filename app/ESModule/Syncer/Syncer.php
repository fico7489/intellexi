<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator;
use App\ESModule\Syncer\Creator\SyncRow\SyncRowsCreator;
use App\ESModule\Syncer\SearchEngine\SearchEngineEsSyncer;

class Syncer
{
    public function __construct(
        private readonly SyncRowsCreator $syncRowsCreator,
        private readonly DocumentsCreator $documentsCreator,
        private readonly SearchEngineEsSyncer $searchEngineSyncer,// TODO by interface
    ) {
    }

    public function sync(CdcDtosEvent $event): void
    {
        $cdcDtos = $event->getCdcDtos();

        $syncRowDtos = $this->syncRowsCreator->create($cdcDtos);

        $documentDtos = $this->documentsCreator->create($syncRowDtos);

        $this->searchEngineSyncer->syncDocuments($documentDtos);
    }
}
