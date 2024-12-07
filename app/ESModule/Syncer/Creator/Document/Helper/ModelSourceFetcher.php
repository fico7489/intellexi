<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class ModelSourceFetcher
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
        private readonly ConfigProvider $syncMapper,
    ) {
    }

    public function fetch(SyncItemDto $syncItemDto): ?object
    {
        $tableName = $syncItemDto->getTableName();

        if (!$this->syncMapper->isTableNameForIndex($tableName)) {
            return null;
        }

        $className = $this->ormAdapter->convertTableNameToClassName($tableName);
        $identifierValue = $syncItemDto->getIdentifierValue();
        $modelSource = $this->ormAdapter->fetchModel($className, $identifierValue);

        return $modelSource;
    }
}
