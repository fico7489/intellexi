<?php

namespace App\ESModule\Syncer\Creator\Document\DocumentsCreator;

use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;

class MatchedSyncItemCreator
{
    public function __construct(
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly IndexMapper $indexMapper,
        private readonly DocumentCreator $documentCreator,
    ) {
    }

    public function createForMatched($syncItemDto, $tableName, $indexName, $changedFieldsTriggers): array
    {
        $documents = [];

        // sync is matched by changed table $syncItemDto and table from $syncMapping
        if ($tableName === $syncItemDto->getTableName()
            && $this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)
        ) {
            $index = $this->indexMapper->fetchIndexByIndexName($indexName);

            return $this->documentCreator->create($syncItemDto, $index);
        }

        return [];
    }
}
