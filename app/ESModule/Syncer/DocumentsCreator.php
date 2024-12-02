<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\Dto\Document;
use App\ESModule\Syncer\SyncItemsFetcher\ItemsFetcher;

class DocumentsCreator
{
    public function __construct(
        private readonly ItemsFetcher $syncItemsFetcher,
    ) {
    }

    /**
     * @return array<Document>
     */
    public function createDocuments(array $changedRowsGrouped): array
    {
        $documents = [];
        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $changedRowGrouped) {
                /* @var SyncRowDto $changedRowGrouped */
                $items = $this->syncItemsFetcher->fetch($changedRowGrouped);

                foreach ($items as $indexName => $data) {
                    $documents[$indexName][] = new Document(
                        $indexName,
                        $changedRowGrouped->getIdentifier(),
                        $data,
                        SyncRowDto::TYPE_DELETE === $changedRowGrouped->getType() ? Document::TYPE_DELETE : Document::TYPE_UPSERT,
                    );
                }
            }
        }

        return $documents;
    }
}
