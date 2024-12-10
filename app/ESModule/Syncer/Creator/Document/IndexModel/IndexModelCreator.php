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
     * @param array<CdcSyncableDto> $cdcSyncableDtos
     */
    public function create(array $cdcSyncableDtos): array
    {
        $indexModelDtosGrouped = [];
        foreach ($cdcSyncableDtos as $cdcSyncableDto) {
            $indexNames = $cdcSyncableDto->getIndexNamesForSync();
            foreach ($indexNames as $indexName) {
                // detect $modelSource
                $modelSource = $this->fetchModelSource($cdcSyncableDto);

                $indexModelDtosGrouped = $this->indexModelItemCreator->create($indexModelDtosGrouped, $cdcSyncableDto, $modelSource, $indexName);
            }
        }

        $indexModelDtosFlattened = $this->indexModelFlattener->flatten($indexModelDtosGrouped);

        return $indexModelDtosFlattened;
    }

    private function fetchModelSource(CdcSyncableDto $cdcSyncableDto): ?object
    {
        $modelSource = null;
        if ($this->configProvider->isTableNameClassNameOrm($cdcSyncableDto->getTableName())) {
            $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($cdcSyncableDto->getTableName());
            $modelSource = $this->ormAdapter->fetchModelByData($cdcSyncableDto, $classNameOrm, $cdcSyncableDto->getIdentifierValue());
        }

        return $modelSource;
    }
}
