<?php

namespace App\ESModule\Syncer\Creator\SyncDocument;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\SyncDocument\Dto\SyncableDocumentDto;
use App\ESModule\Syncer\Creator\SyncDocument\Helper\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\SyncDocument\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncableItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class SyncableDocumentsCreator
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
     * @param array<SyncableItemDto> $syncItemDtos
     *
     * @return array<SyncableDocumentDto>
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

                $syncModels = $this->createSyncModelsForIndexNameRelated($syncModels, $syncItemDto, $modelSource, $indexName);
            }
        }

        return $syncModels;
    }

    private function createSyncModelsForIndexNameRelated(array $syncModels, SyncableItemDto $syncItemDto, $modelSource, $indexNameRelated): array
    {
        $tableNameRelated = $this->configProvider->fetchTableNameByIndexName($indexNameRelated);

        // detect $indexDto
        $indexDtoRelated = $this->configProvider->fetchIndexDtoByIndexName($indexNameRelated);

        $modelsRelated = $this->modelsRelatedFetcher->fetch($syncItemDto, $indexDtoRelated, $modelSource);

        // TODO make updates unique by model->id

        foreach ($modelsRelated as $modelRelated) {
            $type = $syncItemDto->getType();
            if ($modelRelated !== $modelSource) {
                $type = SyncableDocumentDto::TYPE_UPSERT;
            }

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};

            $syncModels[$tableNameRelated][$identifierValue] = [
                'type' => $type,
            ];
        }

        return $syncModels;
    }

    private function fetchModelSource(SyncableItemDto $syncItemDto): ?object
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

    private function filterSyncMappingForTableName(SyncableItemDto $syncItemDto, array $syncMappingForTableName): array
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
