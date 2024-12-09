<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class SyncModelsCreator
{
    private array $modelSources = [];

    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly OrmAdapter $ormAdapter,
        private readonly ModelsRelatedFetcher $modelsRelatedFetcher,
    ) {
    }

    /**
     * @param array<SyncItemDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $syncMapping = $this->configProvider->getConfigDto()->getSyncMap();

        $syncModels = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $tableName = $syncItemDto->getTableName();
            $syncMappingForTableName = $syncMapping[$tableName];
            $syncMappingForTableNameFiltered = $this->filterSyncMappingForTableName($syncItemDto, $syncMappingForTableName);

            foreach ($syncMappingForTableNameFiltered as $indexName => $changedFieldsTriggers) {
                // detect $modelSource
                $modelSource = $this->fetchModelSource($syncItemDto);

                $syncModels = $this->createSyncModelsForIndexName($syncModels, $syncItemDto, $modelSource, $indexName);
            }
        }

        return $syncModels;
    }

    private function createSyncModelsForIndexName(array $syncModels, SyncItemDto $syncItemDto, $modelSource, $indexName): array
    {
        // detect $indexDto
        $indexDto = $this->configProvider->fetchIndexDtoByIndexName($indexName);

        $modelsRelated = $this->modelsRelatedFetcher->fetch($syncItemDto, $indexDto, $modelSource);

        // TODO make updates unique by model->id

        foreach ($modelsRelated as $modelRelated) {
            $type = $syncItemDto->getType();
            if ($modelRelated !== $modelSource) {
                $type = DocumentDto::TYPE_UPSERT;
            }

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};

            $syncModels[$indexDto->getName()][$identifierValue] = [
                'type' => $type,
                'modelRelated' => $modelRelated,
            ];
        }

        return $syncModels;
    }

    private function fetchModelSource(SyncItemDto $syncItemDto): ?object
    {
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

        return $modelSource;
    }

    private function filterSyncMappingForTableName(SyncItemDto $syncItemDto, array $syncMappingForTableName): array
    {
        $syncMappingForTableNameFiltered = [];

        foreach ($syncMappingForTableName as $indexName => $changedFieldsTriggers) {
            // sync is matched by changed table $syncItemDto and table from $syncMapping
            if ($this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)) {
                $syncMappingForTableNameFiltered[$indexName] = $changedFieldsTriggers;
            }
        }

        return $syncMappingForTableNameFiltered;
    }
}
