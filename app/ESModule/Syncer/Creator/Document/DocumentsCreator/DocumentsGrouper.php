<?php

namespace App\ESModule\Syncer\Creator\Document\DocumentsCreator;

use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;

class DocumentsGrouper
{
    /**
     * @param array<DocumentDto> $documents
     */
    public function group(array $documents): array
    {
        // TODO test grouping, add delete different
        // TODO mark document as root in DTO
        // TODO add to document source of trigger
        // TODO group in different service for ESAdapter
        $documentsGrouped = [];
        foreach ($documents as $document) {
            $documentsGrouped[$document->getIndex()][] = $document;
        }

        // TODO exclude duplicates one more time

        return $documentsGrouped;
    }
}
