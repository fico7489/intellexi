<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;

class DocumentsCreator
{
    public function __construct(
        private readonly DocumentsItemCreator $documentsItemCreator,
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
            $documents = $this->documentsItemCreator->fetch($syncRowDto);

            foreach ($documents as $document) {
                $documentsGrouped[$document->getIndex()][] = $document;
            }
        }

        return $documentsGrouped;
    }
}
