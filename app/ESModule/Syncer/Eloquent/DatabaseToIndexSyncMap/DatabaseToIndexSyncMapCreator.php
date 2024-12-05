<?php

namespace App\ESModule\Syncer\Eloquent\DatabaseToIndexSyncMap;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Syncer\Eloquent\ModelMapper;

class DatabaseToIndexSyncMapCreator
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        private readonly ModelMapper $modelMapper,
    ) {
    }

    public function fetchDatabaseToIndexSyncMap(): array
    {
        $databaseMapping = [];

        $indexDefiners = $this->configFetcher->fetchIndexes();
        foreach ($indexDefiners as $indexDefiner) {
            $className = $indexDefiner->getClassName();

            $databaseName = $this->modelMapper->fetchDatabaseNameFromClassName($className);
            $indexName = $indexDefiner->getIndexName();

            $sync = $indexDefiner->getSync();
            foreach ($sync as $syncType) {
                if ($syncType instanceof RootSync) {
                    $tableName = $this->modelMapper->convertClassNameToTable($className);

                    $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $indexName, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedModelSync) {
                    $className = $syncType->getClassName();
                    $tableName = $this->modelMapper->convertClassNameToTable($className);

                    $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $indexName, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedTableSync) {
                    $tableName = $syncType->getTableName();

                    $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $indexName, $tableName, $syncType);
                }
            }
        }

        return $databaseMapping;
    }

    private function addMapping(array $databaseMapping, $databaseName, $indexName, $tableName, $syncType): array
    {
        $databaseMapping[$databaseName][$tableName][] = [
            'index' => $this->modelMapper->detectIndexDefinerByName($indexName),
            'type' => $syncType,
        ];

        return $databaseMapping;
    }

    public function getSyncTableNames(): array
    {
        $databaseToIndexSyncMap = $this->fetchDatabaseToIndexSyncMap();

        $syncTableNames = [];
        foreach ($databaseToIndexSyncMap as $databaseName => $data) {
            foreach ($data as $tableName => $items) {
                $syncTableNames[$databaseName][$tableName] = true;
            }
        }

        return $syncTableNames;
    }

    public function isSyncDatabaseNameAndTableName($databaseName, $tableName): bool
    {
        $syncTableNames = $this->getSyncTableNames();

        return isset($syncTableNames[$databaseName][$tableName]);
    }
}
