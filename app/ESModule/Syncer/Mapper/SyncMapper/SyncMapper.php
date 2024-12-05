<?php

namespace App\ESModule\Syncer\Mapper\SyncMapper;

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
            // TODO [], syncMap([])
            $syncMap = $indexDefiner->syncMap([]);
            foreach ($syncMap as $classNameSyncMap => $changedFields) {
                $tableName = $classNameSyncMap;
                if ($this->modelMapper->isClassNameModel($classNameSyncMap)) {
                    $tableName = $this->modelMapper->convertClassNameToTableName($classNameSyncMap);
                }

                $indexName = $indexDefiner->getIndexName();

                $syncMapping[$tableName][$indexName] = $changedFields;
            }
        }

        return $syncMapping;
    }

    public function getTableNamesSync(): array
    {
        $syncMapping = $this->create();

        $tableNamesSync = [];
        foreach ($syncMapping as $tableName => $items) {
            $tableNamesSync[$tableName] = true;
        }

        return $tableNamesSync;
    }

    public function isDatabaseNameForSync($tableName): bool
    {
        //TODO
        return true;
    }

    public function isTableNameForSync($tableName): bool
    {
        $tableNamesSync = $this->getTableNamesSync();

        return isset($tableNamesSync[$tableName]);
    }

    public function getTableNamesIndex(): array
    {
        $classNamesIndex = $this->indexMapper->fetchClassNamesIndex();

        $tableNamesIndex = [];
        foreach ($classNamesIndex as $className => $indexDefiner) {
            $tableName = $this->modelMapper->convertClassNameToTableName($className);

            $tableNamesIndex[$tableName] = true;
        }

        return $tableNamesIndex;
    }

    public function isTableNameForIndex($tableName): bool
    {
        $tableNamesIndex = $this->getTableNamesIndex();

        return isset($tableNamesIndex[$tableName]);
    }
}
