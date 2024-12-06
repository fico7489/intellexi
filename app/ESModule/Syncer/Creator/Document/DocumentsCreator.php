<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentCreator;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentsGrouper;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;

class DocumentsCreator
{
    public function __construct(
        private readonly SyncMapper $syncMapper,
        private readonly IndexMapper $indexMapper,
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly DocumentCreator $syncItemDocumentCreator,
        private readonly DocumentsGrouper $documentsGrouper,
    ) {
    }

    /**
     * @param array<SyncItemDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $syncMapping = $this->syncMapper->create();

        $documents = [];
        foreach ($syncItemDtos as $syncItemDto) {
            foreach ($syncMapping as $tableName => $indexData) {
                foreach ($indexData as $indexName => $changedFieldsTriggers) {
                    // sync is matched by changed table $syncItemDto and table from $syncMapping
                    if ($tableName === $syncItemDto->getTableName()
                        && $this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)
                    ) {
                        // TODO add $index to updating map
                        $index = $this->indexMapper->fetchIndexByIndexName($indexName);

                        $documentsNew = $this->syncItemDocumentCreator->create($syncItemDto, $index);
                        $documents = array_merge($documents, $documentsNew);
                    }
                }
            }
        }

        return $this->documentsGrouper->group($documents);
    }
}
