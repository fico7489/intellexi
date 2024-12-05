<?php

namespace App\ESModule\Syncer\Mapper\SyncMapper;

use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class SyncMapper
{
    public function __construct(
        private readonly IndexMapper $indexMapper,
        private readonly ModelMapper $modelMapper,
    ) {
    }

    public function create(): array
    {
        $syncMapping = [];

        $indexDefiners = $this->indexMapper->fetchClassNamesIndex();
        foreach ($indexDefiners as $className => $indexDefiner) {
            $sync = $indexDefiner->getSync();
            foreach ($sync as $syncType) {
                if ($syncType instanceof RootSync) {
                    $tableName = $this->modelMapper->convertClassNameToTableName($className);

                    $syncMapping = $this->addMapping($syncMapping, $indexDefiner, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedModelSync) {
                    $className = $syncType->getClassName();
                    $tableName = $this->modelMapper->convertClassNameToTableName($className);

                    $syncMapping = $this->addMapping($syncMapping, $indexDefiner, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedTableSync) {
                    $tableName = $syncType->getTableName();

                    $syncMapping = $this->addMapping($syncMapping, $indexDefiner, $tableName, $syncType);
                }
            }
        }

        return $syncMapping;
    }

    private function addMapping(array $databaseMapping, $indexDefiner, $tableName, $syncType): array
    {
        $databaseMapping[$tableName][] = [
            'index' => $indexDefiner,
            'type' => $syncType,
        ];

        return $databaseMapping;
    }

    public function getSyncTableNames(): array
    {
        $databaseToIndexSyncMap = $this->create();

        $syncTableNames = [];
        foreach ($databaseToIndexSyncMap as $tableName => $items) {
            $syncTableNames[$tableName] = true;
        }

        return $syncTableNames;
    }

    public function isTableNameForSync($tableName): bool
    {
        $syncTableNames = $this->getSyncTableNames();

        return isset($syncTableNames[$tableName]);
    }
}
