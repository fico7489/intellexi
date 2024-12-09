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
        if ($this->configProvider->isTableNameClassNameOrm($syncItemDto->getTableName())) {
            $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($syncItemDto->getTableName());
            $modelSource = $this->ormAdapter->fetchModelByData($syncItemDto, $classNameOrm, $syncItemDto->getIdentifierValue());
        }

        return $modelSource;
    }
}
