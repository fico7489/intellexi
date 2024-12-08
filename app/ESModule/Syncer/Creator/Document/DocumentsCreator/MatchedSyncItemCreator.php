<?php

namespace App\ESModule\Syncer\Creator\Document\DocumentsCreator;

use App\ESModule\Syncer\Creator\Document\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class MatchedSyncItemCreator
{
    public function __construct(
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly ConfigProvider $configProvider,
        private readonly DocumentCreator $documentCreator,
        private readonly ModelSourceFetcher $modelSourceFetcher,
    ) {
    }

    public function createForMatched(SyncItemDto $syncItemDto, $tableName, $indexName, $changedFieldsTriggers): array
    {
        if ($tableName !== $syncItemDto->getTableName()) {
            return [];
        }

        // sync is matched by changed table $syncItemDto and table from $syncMapping
        if (!$this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)) {
            return [];
        }

        $index = $this->configProvider->fetchIndexByIndexName($indexName);

        $modelSource = $this->modelSourceFetcher->fetch($syncItemDto);
        // dump($syncItemDto->getTableName(), $syncItemDto->getIdentifierValue(), $syncItemDto->getChangedFields(), is_null($modelSource));

        return $this->documentCreator->create($syncItemDto, $index, $modelSource);
    }
}
