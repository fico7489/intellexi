<?php

namespace App\ESModule\Syncer\Eloquent\DatabaseToIndexSyncMap;

use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class DatabaseToIndexSyncMapCreator
{
    public function __construct(
        private readonly IndexMapper $indexMapper,
        private readonly ModelMapper $modelMapper,
    ) {
    }

    public function create(): array
    {
        $databaseMapping = [];

        $indexDefiners = $this->indexMapper->fetchClassNamesIndex();
        foreach ($indexDefiners as $className => $indexDefiner) {
            $sync = $indexDefiner->getSync();
            foreach ($sync as $syncType) {
                if ($syncType instanceof RootSync) {
                    $tableName = $this->modelMapper->convertClassNameToTableName($className);

                    $databaseMapping = $this->addMapping($databaseMapping, $indexDefiner, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedModelSync) {
                    $className = $syncType->getClassName();
                    $tableName = $this->modelMapper->convertClassNameToTableName($className);

                    $databaseMapping = $this->addMapping($databaseMapping, $indexDefiner, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedTableSync) {
                    $tableName = $syncType->getTableName();

                    $databaseMapping = $this->addMapping($databaseMapping, $indexDefiner, $tableName, $syncType);
                }
            }
        }

        return $databaseMapping;
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

    public function isSyncDatabaseNameAndTableName($databaseName, $tableName): bool
    {
        $syncTableNames = $this->getSyncTableNames();

        return isset($syncTableNames[$tableName]);
    }
}
