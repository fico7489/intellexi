<?php

namespace App\ESModule\Syncer\Mapper\SyncMapper;

use App\ES\Connection\DefaultConnection;
use App\ES\Index\Model\ApplicationIndex;
use App\ES\Index\Model\UserIndex;
use App\ESModule\Config\Dto\ConnectionDto;
use App\ESModule\Config\Dto\IndexDto;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;

class SyncMapper
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    // TODO interface
    /**
     * @return array<DefaultConnection>
     */
    public function fetchConnections(): array
    {
        return [
            app(DefaultConnection::class),
        ];
    }

    /**
     * @return array<IndexDefinerModelInterface>
     */
    public function fetchIndexes(): array
    {
        // TODO load by attributes
        return [
            app(ApplicationIndex::class),
            app(UserIndex::class),
        ];
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
        $indexDefiners = $this->fetchIndexes();

        $classNamesIndex = [];

        foreach ($indexDefiners as $indexDefiner) {
            $classNamesIndex[$indexDefiner->getClassName()] = $indexDefiner;
        }

        return $classNamesIndex;
    }

    public function fetchIndexByIndexName(string $indexName): IndexDefinerModelInterface
    {
        $classNamesIndex = $this->fetchClassNamesIndex();

        $indexDefiners = $this->fetchIndexes();
        foreach ($indexDefiners as $indexDefiner) {
            if ($indexDefiner->getIndexName() === $indexName) {
                return $indexDefiner;
            }
        }

        // TODO
    }

    /**
     * @return array<ConnectionDto>
     */
    public function buildConfigMap(): array
    {
        $connectionsDefiners = $this->fetchConnections();
        $indexDefiners = $this->fetchIndexes();

        $connectionDtos = [];
        foreach ($connectionsDefiners as $connectionDefiner) {
            /** @var DefaultConnection $connectionDefiner */
            $connection = new ConnectionDto(
                $connectionDefiner->getName(),
                $connectionDefiner->getHost(),
                $connectionDefiner->getPort(),
                $connectionDefiner->getPrefix(),
            );

            $indexes = [];
            foreach ($indexDefiners as $indexDefiner) {
                if ($indexDefiner->getConnection() === $connection->getName()) {
                    $indexes[] = new IndexDto(
                        $indexDefiner->getIndexName(),
                        $indexDefiner->getMapping([]),
                        $indexDefiner->getSettings([]),
                        $connection
                    );
                }
            }

            $connection->setIndexes($indexes);

            $connectionDtos[] = $connection;
        }

        return $connectionDtos;
    }
}
