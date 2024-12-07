<?php

namespace App\ESModule\Syncer\Provider;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Dto\ConnectionDto;
use App\ESModule\Config\Dto\IndexDto;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;

class ConfigProvider
{
    /**
     * @param array<IndexDefinerModelInterface> $configIndexes
     */
    public function __construct(
        // TODO
        private readonly DefaultConnection $configConnection,
        private readonly array $configIndexes,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    public function buildSyncMapping(): array
    {
        $syncMapping = [];

        $indexDefiners = $this->fetchClassNamesIndex();
        foreach ($indexDefiners as $className => $indexDefiner) {
            // TODO [], syncMap([]) DTO
            $syncMap = $indexDefiner->syncMap([]);
            foreach ($syncMap as $classNameSyncMap => $changedFields) {
                $tableName = $classNameSyncMap;
                if ($this->ormAdapter->isClassNameModel($classNameSyncMap)) {
                    $tableName = $this->ormAdapter->convertClassNameToTableName($classNameSyncMap);
                }

                $indexName = $indexDefiner->getIndexName();

                $syncMapping[$tableName][$indexName] = $changedFields;
            }
        }

        return $syncMapping;
    }

    public function getTableNamesSync(): array
    {
        $syncMapping = $this->buildSyncMapping();

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
            $tableName = $this->ormAdapter->convertClassNameToTableName($className);

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
        $indexDefiners = $this->configIndexes;

        $classNamesIndex = [];

        foreach ($indexDefiners as $indexDefiner) {
            $classNamesIndex[$indexDefiner->getClassName()] = $indexDefiner;
        }

        return $classNamesIndex;
    }

    public function fetchIndexByIndexName(string $indexName): IndexDefinerModelInterface
    {
        $classNamesIndex = $this->fetchClassNamesIndex();

        $indexDefiners = $this->configIndexes;
        foreach ($indexDefiners as $indexDefiner) {
            if ($indexDefiner->getIndexName() === $indexName) {
                return $indexDefiner;
            }
        }

        // TODO
    }

    public function buildConnectionDto(): ConnectionDto
    {
        $connectionDefiner = $this->configConnection;

        $connection = new ConnectionDto(
            $connectionDefiner->getName(),
            $connectionDefiner->getHost(),
            $connectionDefiner->getPort(),
            $connectionDefiner->getPrefix(),
        );

        $indexDefiners = $this->configIndexes;
        $indexes = [];
        foreach ($indexDefiners as $indexDefiner) {
            $indexes[$indexDefiner->getIndexName()] = new IndexDto(
                $indexDefiner->getIndexName(),
                $indexDefiner->getMapping([]),
                $indexDefiner->getSettings([]),
                $connection
            );
        }

        $connection->setIndexes($indexes);

        return $connection;
    }
}
