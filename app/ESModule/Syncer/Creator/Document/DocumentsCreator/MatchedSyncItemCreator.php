<?php

namespace App\ESModule\Syncer\Creator\Document\DocumentsCreator;

use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class MatchedSyncItemCreator
{
    public function __construct(
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly ConfigProvider $configProvider,
        private readonly DocumentCreator $documentCreator,
    ) {
    }

    public function createForMatched(SyncItemDto $syncItemDto, $tableName, $indexName, $changedFieldsTriggers): array
    {
        if ($tableName !== $syncItemDto->getTableName()) {
            dump(1111);

            return [];
        }

        // sync is matched by changed table $syncItemDto and table from $syncMapping
        dump($tableName, $syncItemDto->getChangedFields(), $changedFieldsTriggers);
        if (!$this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)) {
            dump(2222);

            return [];
        }

        dump(3333);
        $index = $this->configProvider->fetchIndexByIndexName($indexName);

        return $this->documentCreator->create($syncItemDto, $index);
    }
}
