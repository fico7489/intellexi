<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentCreator;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentsGrouper;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class DocumentsCreator
{
    private array $modelSources = [];

    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly DocumentsGrouper $documentsGrouper,
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly DocumentCreator $documentCreator,
        private readonly OrmAdapter $ormAdapter,
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
                    $documents = $this->createForMatched($documents, $syncItemDto, $tableName, $indexName, $changedFieldsTriggers);
                }
            }
        }

        dd(1112, $documents);

        // return  $documents;
        return $this->documentsGrouper->group($documents);
    }

    private function createForMatched(array $documents, SyncItemDto $syncItemDto, $tableName, $indexName, $changedFieldsTriggers): array
    {
        if ($tableName !== $syncItemDto->getTableName()) {
            return $documents;
        }

        // sync is matched by changed table $syncItemDto and table from $syncMapping
        if (!$this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)) {
            return $documents;
        }

        $index = $this->configProvider->fetchIndexByIndexName($indexName);

        $modelSource = null;
        $tableName = $syncItemDto->getTableName();
        $identifierValue = $syncItemDto->getIdentifierValue();
        if ($this->configProvider->isTableNameClassNameOrm($tableName)) {
            if (!isset($this->modelSources[$tableName][$identifierValue])) {
                $tableName = $syncItemDto->getTableName();

                if ($this->configProvider->isTableNameClassNameOrm($tableName)) {
                    $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($tableName);
                    $modelSource = $this->ormAdapter->fetchModel($syncItemDto, $classNameOrm);
                }

                $this->modelSources[$tableName][$identifierValue] = $modelSource;
            }

            $modelSource = $this->modelSources[$tableName][$identifierValue];
        }

        return $this->documentCreator->create($documents, $syncItemDto, $index, $modelSource);
    }
}
