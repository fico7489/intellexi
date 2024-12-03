<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\Dto\Document;
use App\ESModule\Syncer\SyncItemsFetcher\ItemsFetcher;
use App\ESModule\Syncer\SyncItemsFetcher\ItemsRelatedModelsFetcher;

class DocumentsCreator
{
    public function __construct(
        private readonly ItemsRelatedModelsFetcher $itemsRelatedModelsFetcher,
    ) {
    }

    /**
     * @return array<Document>
     */
    public function createDocuments(array $changedRowsGrouped): array
    {
        $documents = [];
        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $syncRowDto) {
                /* @var SyncRowDto $syncRowDto */
                $items = $this->itemsRelatedModelsFetcher->fetch($syncRowDto);

                foreach ($items as $indexName => $item) {
                    foreach ($item as $identifier => $data) {
                        $documents[$indexName][] = new Document(
                            $indexName,
                            $identifier,
                            $data,
                            SyncRowDto::TYPE_DELETE === $syncRowDto->getType() ? Document::TYPE_DELETE : Document::TYPE_UPSERT,
                        );
                    }
                }
            }
        }

        return $documents;
    }
}
