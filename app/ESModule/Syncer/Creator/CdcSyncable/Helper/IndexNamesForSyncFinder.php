<?php

namespace App\ESModule\Syncer\Creator\CdcSyncable\Helper;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class IndexNamesForSyncFinder
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
    ) {
    }

    public function findIndexNamesForSync(CdcDto $cdcDto): array
    {
        // don't do sync if database is not supported
        $databaseName = $cdcDto->getDatabaseName();
        if (!$this->configProvider->isDatabaseNameForSync($databaseName)) {
            return [];
        }

        $tableName = $cdcDto->getTableName();
        $syncMap = $this->configProvider->getConfigDto()->getSyncMap();
        $syncMapForTableName = $syncMap[$tableName] ?? [];

        // don't do sync if tableName is not is for sync
        if (0 === count($syncMapForTableName)) {
            return [];
        }

        $type = $cdcDto->getType();
        $changedFields = $cdcDto->getChangedFields();

        $indexNamesForSync = [];
        foreach ($syncMapForTableName as $indexName => $changedFieldsTriggers) {
            if ($this->shouldSync($type, $changedFields, $changedFieldsTriggers)) {
                $indexNamesForSync[] = $indexName;
            }
        }

        return $indexNamesForSync;
    }

    public function shouldSync(string $type, array $changedFieldsTriggered, array $changedFieldsTriggers): bool
    {
        if (in_array($type, [CdcDto::TYPE_INSERT, CdcDto::TYPE_DELETE])) {
            return true;
        }

        return !empty(array_intersect($changedFieldsTriggered, $changedFieldsTriggers));
    }
}
