<?php

namespace App\ESModule\Syncer\Mapper\SyncMapper;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Adapter\OrmAdapter\OrmMapper;

class SyncMapper
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        private readonly OrmMapper $modelMapper,
    ) {
    }

    public function create(): array
    {
        $syncMapping = [];

        $indexDefiners = $this->fetchClassNamesIndex();
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
        // TODO
        return true;
    }

    public function isTableNameForSync($tableName): bool
    {
        $tableNamesSync = $this->getTableNamesSync();

        return isset($tableNamesSync[$tableName]);
    }

    public function getTableNamesIndex(): array
    {
        $classNamesIndex = $this->fetchClassNamesIndex();

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

    /**
     * @return array<IndexDefinerModelInterface>
     */
    public function fetchClassNamesIndex(): array
    {
        $indexDefiners = $this->configFetcher->fetchIndexes();

        $classNamesIndex = [];

        foreach ($indexDefiners as $indexDefiner) {
            $classNamesIndex[$indexDefiner->getClassName()] = $indexDefiner;
        }

        return $classNamesIndex;
    }

    public function fetchIndexByIndexName(string $indexName): IndexDefinerModelInterface
    {
        $classNamesIndex = $this->fetchClassNamesIndex();

        $indexDefiners = $this->configFetcher->fetchIndexes();
        foreach ($indexDefiners as $indexDefiner) {
            if ($indexDefiner->getIndexName() === $indexName) {
                return $indexDefiner;
            }
        }

        // TODO
    }
}
