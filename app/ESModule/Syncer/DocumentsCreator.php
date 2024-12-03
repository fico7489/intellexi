<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\Dto\DocumentDto;
use App\ESModule\Syncer\SyncItemsFetcher\ItemsRelatedModelsFetcher;

class DocumentsCreator
{
    public function __construct(
        private readonly ItemsRelatedModelsFetcher $itemsRelatedModelsFetcher,
    ) {
    }

    /**
     * @param array<SyncRowDto> $syncRowDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncRowDtos): array
    {
        $documentsGrouped = [];
        foreach ($syncRowDtos as $syncRowDto) {
            /* @var SyncRowDto $syncRowDto */
            $documents = $this->itemsRelatedModelsFetcher->fetch($syncRowDto);

            foreach ($documents as $document) {
                $documentsGrouped[$document->getIndex()][] = $document;
            }
        }

        return $documentsGrouped;
    }
}
