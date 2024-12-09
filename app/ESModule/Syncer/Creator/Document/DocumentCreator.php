<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ModelsRelatedFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;

class DocumentCreator
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly OrmAdapter $ormAdapter,
        private readonly ModelsRelatedFetcher $modelsRelatedFetcher,
        private readonly EsDocumentsCreator $documentsCreator,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     */
    public function create(array $syncItemDtos): array
    {
        $syncMapping = $this->configProvider->getConfigDto()->getSyncMap();

        $syncModels = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $tableName = $syncItemDto->getTableName();
            $indexNames = $syncItemDto->getIndexNamesForSync();

            foreach ($indexNames as $indexName) {
                // detect $modelSource
                $modelSource = $this->fetchModelSource($syncItemDto);

                $syncModels = $this->createSyncModelsForIndexNameRelated($syncModels, $syncItemDto, $modelSource, $indexName);
            }
        }

        $syncModelsCollapsed = [];
        foreach ($syncModels as $tableNameRelated => $data) {
            foreach ($data as $identifierValue => $dto) {
                $syncModelsCollapsed[] = $dto;
            }
        }

        $documents = $this->documentsCreator->create($syncModelsCollapsed);

        return $documents;
    }

    private function createSyncModelsForIndexNameRelated(array $syncModels, CdcSyncableDto $syncItemDto, $modelSource, $indexNameRelated): array
    {
        $tableNameRelated = $this->configProvider->fetchTableNameByIndexName($indexNameRelated);

        // detect $indexDto
        $indexDtoRelated = $this->configProvider->fetchIndexDtoByIndexName($indexNameRelated);

        $modelsRelated = $this->modelsRelatedFetcher->fetch($syncItemDto, $indexDtoRelated, $modelSource);

        // TODO make updates unique by model->id

        foreach ($modelsRelated as $modelRelated) {
            $type = $syncItemDto->getType();
            if ($modelRelated !== $modelSource) {
                $type = DocumentDto::TYPE_UPSERT;
            }

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};

            $syncModels[$tableNameRelated][$identifierValue] = [
                'type' => $type,
            ];

            // $syncModels[$tableNameRelated][$identifierValue] = new SyncableDocumentDto($indexNameRelated, $identifierValue, $type);

            $syncModels[$tableNameRelated][$identifierValue] = [
                'indexName' => $indexNameRelated,
                'identifierValue' => $identifierValue,
                'type' => $type,
            ];
        }

        return $syncModels;
    }

    private function fetchModelSource(CdcSyncableDto $syncItemDto): ?object
    {
        $modelSource = null;
        $tableName = $syncItemDto->getTableName();
        $identifierValue = $syncItemDto->getIdentifierValue();
        if ($this->configProvider->isTableNameClassNameOrm($tableName)) {
            if (!isset($this->modelSources[$tableName][$identifierValue])) {
                $tableName = $syncItemDto->getTableName();

                if ($this->configProvider->isTableNameClassNameOrm($tableName)) {
                    $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($tableName);
                    $modelSource = $this->ormAdapter->fetchModel($classNameOrm, $syncItemDto->getIdentifierValue());
                }

                $this->modelSources[$tableName][$identifierValue] = $modelSource;
            }

            $modelSource = $this->modelSources[$tableName][$identifierValue];
        }

        return $modelSource;
    }
}
