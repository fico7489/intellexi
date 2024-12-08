<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Provider\ConfigProvider;

class ModelSourceFetcher
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
        private readonly ConfigProvider $configProvider,
    ) {
    }

    public function fetch(string $tableName, mixed $identifierValue): ?object
    {
        dump(4444);

        if (!$this->configProvider->isTableNameClassNameOrm($tableName)) {
            return null;
        }

        $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($tableName);
        $modelSource = $this->ormAdapter->fetchModel($classNameOrm, $identifierValue);

        return $modelSource;
    }
}
