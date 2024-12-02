<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Syncer\Dto\Document;

class DocumentsCreator
{
    public function __construct(
        private readonly SyncDataDetector $syncDataDetector,
    ) {
    }

    public function createDocuments($changedRowsGrouped): array
    {
        $documents = [];
        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $changedRowGrouped) {
                /* @var ChangedRowGroupedDto $changedRowGrouped */
                $items = $this->syncDataDetector->detect($changedRowGrouped);

                foreach ($items as $indexName => $data) {
                    $documents[$indexName][] = new Document(
                        $indexName,
                        $changedRowGrouped->getIdentifier(),
                        $data,
                        ChangedRowDto::TYPE_DELETE === $changedRowGrouped->getType() ? Document::TYPE_DELETE : Document::TYPE_UPSERT,
                    );
                }
            }
        }

        return $documents;
    }
}
