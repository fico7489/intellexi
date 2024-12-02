<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Event\CdcChangedRowsGrouped;

class Syncer
{
    public function __construct(
        private readonly DocumentsCreator     $documentsCreator,
        private readonly SearchEngineEsSyncer $searchEngineSyncer,// TODO by interface
    ) {
    }

    public function sync(CdcChangedRowsGrouped $event): void
    {
        $changedRowsGrouped = $event->getChangedRowsGrouped();

        $documents = $this->documentsCreator->createDocuments($changedRowsGrouped);

        $this->searchEngineSyncer->syncDocuments($documents);
    }
}
