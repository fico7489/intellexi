<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\Document\Helper\SyncItemDocumentCreator;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;

class DocumentsCreator
{
    public function __construct(
        private readonly SyncMapper $syncMapper,
        private readonly IndexMapper $indexMapper,
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly SyncItemDocumentCreator $syncItemDocumentCreator,
    ) {
    }

    /**
     * @param array<SyncItemDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $documents = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $documents = array_merge($documents, $this->createForItem($syncItemDto));
        }

        $documentsGrouped = [];
        foreach ($documents as $document) {
            $documentsGrouped[$document->getIndex()][] = $document;
        }

        // TODO exclude duplicates one more time

        return $documentsGrouped;
    }

    /**
     * @return array<DocumentDto>
     */
    public function createForItem(SyncItemDto $syncItemDto): array
    {
        $documents = [];
        $syncMapping = $this->syncMapper->create();
        foreach ($syncMapping as $tableName => $indexData) {
            foreach ($indexData as $indexName => $changedFieldsTriggers) {
                // sync is matched by changed table $syncItemDto and table from $syncMapping
                if ($tableName === $syncItemDto->getTableName()
                    && $this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)
                ) {
                    $index = $this->indexMapper->fetchIndexByIndexName($indexName);
                    $className = $index->getClassName();

                    $documents = array_merge($documents, $this->syncItemDocumentCreator->create($syncItemDto, $index));
                }
            }
        }

        return $documents;
    }
}
