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

                    $syncMapping = $this->addSyncMappingItem($syncMapping, $indexDefiner, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedModelSync) {
                    $className = $syncType->getClassName();
                    $tableName = $this->modelMapper->convertClassNameToTableName($className);

                    $syncMapping = $this->addSyncMappingItem($syncMapping, $indexDefiner, $tableName, $syncType);
                }

                if ($syncType instanceof RelatedTableSync) {
                    $tableName = $syncType->getTableName();

                    $syncMapping = $this->addSyncMappingItem($syncMapping, $indexDefiner, $tableName, $syncType);
                }
            }
        }

        return $syncMapping;
    }

    private function addSyncMappingItem(array $syncMapping, $indexDefiner, $tableName, $syncType): array
    {
        $syncMapping[$tableName][] = [
            'index' => $indexDefiner,
            'type' => $syncType,
        ];

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
