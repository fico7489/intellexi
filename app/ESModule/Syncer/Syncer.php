<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Event\CdcChangedRowsGrouped;

class Syncer
{
    public function __construct(
        private readonly DocumentsCreator $documentsCreator,
        private readonly EsSyncer $esSyncer,
    ) {
    }

    public function sync(CdcChangedRowsGrouped $event): void
    {
        $changedRowsGrouped = $event->getChangedRowsGrouped();

        $documents = $this->documentsCreator->createDocuments($changedRowsGrouped);

        $this->esSyncer->esIndexesSync($documents);
    }
}
