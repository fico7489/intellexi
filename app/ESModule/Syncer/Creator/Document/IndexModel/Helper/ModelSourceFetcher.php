<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel\Helper;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class ModelSourceFetcher
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    public function fetch(CdcSyncableDto $cdcSyncableDto): ?object
    {
        $modelSource = null;
        if ($this->configProvider->isTableNameClassNameOrm($cdcSyncableDto->getTableName())) {
            $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($cdcSyncableDto->getTableName());
            $modelSource = $this->ormAdapter->fetchModelByData($cdcSyncableDto, $classNameOrm, $cdcSyncableDto->getIdentifierValue());
        }

        return $modelSource;
    }
}
