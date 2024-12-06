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
        private readonly DocumentCreator $documentCreator,
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
                    $documents = array_merge($documents, $this->createForMatched($syncItemDto, $tableName, $indexName, $changedFieldsTriggers));
                }
            }
        }

        $this->testTwo();

        //return  $documents;
        return $this->documentsGrouper->group($documents);
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

    public function testOne()
    {
        $this->testTwo();
    }

    public function testTwo()
    {
        dump('testTwo');
    }
}
