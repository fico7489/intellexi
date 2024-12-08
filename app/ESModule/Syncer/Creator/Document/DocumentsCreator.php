<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentCreator;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentsGrouper;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class DocumentsCreator
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly DocumentsGrouper $documentsGrouper,
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly DocumentCreator $documentCreator,
        private readonly ModelSourceFetcher $modelSourceFetcher,
    ) {
    }

    /**
     * @param array<SyncItemDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        dump('count $syncItemDtos='.count($syncItemDtos));

        $syncMapping = $this->configProvider->getConfigDto()->getSyncMap();

        $documents = [];
        foreach ($syncItemDtos as $syncItemDto) {
            foreach ($syncMapping as $tableName => $indexData) {
                foreach ($indexData as $indexName => $changedFieldsTriggers) {
                    dump(2222);
                    $documents = array_merge($documents, $this->createForMatched($syncItemDto, $tableName, $indexName, $changedFieldsTriggers));
                }
            }
        }

        dump($documents);

        // return  $documents;
        return $this->documentsGrouper->group($documents);
    }

    private function createForMatched($syncItemDto, $tableName, $indexName, $changedFieldsTriggers): array
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
