<?php

namespace App\ESModule\Syncer\Provider\Builder;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;

class SyncMapBuilder
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    public function buildSyncMapping(array $indexDefiners): array
    {
        $syncMapping = [];

        foreach ($indexDefiners as $className => $indexDefiner) {
            // TODO [], syncMap([]) DTO
            $syncMap = $indexDefiner->syncMap([]);
            foreach ($syncMap as $classNameSyncMap => $changedFields) {
                $tableName = $classNameSyncMap;
                if ($this->ormAdapter->isClassNameOrm($classNameSyncMap)) {
                    $tableName = $this->ormAdapter->convertClassNameOrmToTableName($classNameSyncMap);
                }

                $indexName = $indexDefiner->getIndexName();

                $syncMapping[$tableName][$indexName] = $changedFields;
            }
        }

        return $syncMapping;
    }
}
