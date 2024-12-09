<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Helper\ModelMapCreator;
use App\ESModule\Syncer\Provider\ConfigProvider;

class DocumentCreator
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly OrmAdapter $ormAdapter,
        private readonly EsDocumentsCreator $documentsCreator,
        private readonly ModelMapCreator $modelMapCreator,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     */
    public function create(array $syncItemDtos): array
    {
        $modelMapDtos = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $indexNames = $syncItemDto->getIndexNamesForSync();
            foreach ($indexNames as $indexName) {
                // detect $modelSource
                $modelSource = $this->fetchModelSource($syncItemDto);

                $modelMapDtos = $this->modelMapCreator->createModelMapDtosForIndex($modelMapDtos, $syncItemDto, $modelSource, $indexName);
            }
        }

        $modelMapDtosFlattened = [];
        foreach ($modelMapDtos as $tableNameRelated => $data) {
            foreach ($data as $identifierValue => $dto) {
                $modelMapDtosFlattened[] = $dto;
            }
        }

        $modelMapDtos = $this->documentsCreator->create($modelMapDtosFlattened);

        return $modelMapDtos;
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
