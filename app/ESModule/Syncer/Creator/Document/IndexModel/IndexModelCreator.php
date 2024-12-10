<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class IndexModelCreator
{
    public function __construct(
        private readonly IndexModelFlattener $indexModelFlattener,
        private readonly IndexModelItemCreator $indexModelItemCreator,
        private readonly ConfigProvider $configProvider,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     */
    public function create(array $syncItemDtos): array
    {
        $indexModelDtosGrouped = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $indexNames = $syncItemDto->getIndexNamesForSync();
            foreach ($indexNames as $indexName) {
                // detect $modelSource
                $modelSource = $this->fetchModelSource($syncItemDto);

                $indexModelDtosGrouped = $this->indexModelItemCreator->create($indexModelDtosGrouped, $syncItemDto, $modelSource, $indexName);
            }
        }

        $indexModelDtosFlattened = $this->indexModelFlattener->flatten($indexModelDtosGrouped);

        return $indexModelDtosFlattened;
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
