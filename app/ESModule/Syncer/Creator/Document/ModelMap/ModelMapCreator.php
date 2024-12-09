<?php

namespace App\ESModule\Syncer\Creator\Document\ModelMap;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class ModelMapCreator
{
    public function __construct(
        private readonly ModelMapFlattener $modelMapFlattener,
        private readonly ModelMapItemCreator $modelMapCreator,
        private readonly ConfigProvider $configProvider,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     */
    public function create(array $syncItemDtos): array
    {
        $modelMapDtosGrouped = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $indexNames = $syncItemDto->getIndexNamesForSync();
            foreach ($indexNames as $indexName) {
                // detect $modelSource
                $modelSource = $this->fetchModelSource($syncItemDto);

                $modelMapDtosGrouped = $this->modelMapCreator->createModelMapDtosForIndex($modelMapDtosGrouped, $syncItemDto, $modelSource, $indexName);
            }
        }

        $modelMapDtosFlattened = $this->modelMapFlattener->flatten($modelMapDtosGrouped);

        return $modelMapDtosFlattened;
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
                    $modelSource = $this->ormAdapter->fetchModelByData($syncItemDto, $classNameOrm, $syncItemDto->getIdentifierValue());
                }

                $this->modelSources[$tableName][$identifierValue] = $modelSource;
            }

            $modelSource = $this->modelSources[$tableName][$identifierValue];
        }

        return $modelSource;
    }
}
