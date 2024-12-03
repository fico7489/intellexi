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
    )
    {
    }

    /**
     * @return array<Document>
     */
    public function createDocuments(array $changedRowsGrouped): array
    {
        $documents2 = [];
        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $syncRowDto) {
                /* @var SyncRowDto $syncRowDto */
                $documents = $this->itemsRelatedModelsFetcher->fetch($syncRowDto);

                foreach ($documents as $document) {
                    $documents2[$document->getIndex()][] = $document;
                }
            }
        }

        return $documents2;
    }
}
