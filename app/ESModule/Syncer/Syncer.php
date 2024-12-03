<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Syncer\CdcConverter\CdcConverter;

class Syncer
{
    public function __construct(
        private CdcConverter $cdcConverter,
        private DocumentsCreator $documentsCreator,
        private SearchEngineEsSyncer $searchEngineSyncer,// TODO by interface
    ) {
    }

    public function sync(CdcDtosEvent $event): void
    {
        $cdcDtos = $event->getCdcDtos();

        $syncRowDtos = $this->cdcConverter->convert($cdcDtos);

        $documents = $this->documentsCreator->createDocuments($syncRowDtos);

        $this->searchEngineSyncer->syncDocuments($documents);
    }
}
