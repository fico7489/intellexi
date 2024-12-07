<?php

namespace App\ESModule\Syncer\Provider;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Dto\ConnectionDto;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Provider\Builder\ConnectionDtoBuilder;

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
        private readonly ConnectionDtoBuilder $connectionDtoBuilder,
    ) {
    }

    public function isDatabaseNameForSync($tableName): bool
    {
        // TODO
        return true;
    }

    public function isTableNameForSync($tableName): bool
    {
        $tableNamesSync = $this->getConnectionDto()->getTableNamesSync();

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

    public function getConnectionDto(): ConnectionDto
    {
        return $this->connectionDtoBuilder->build($this->configConnection, $this->configIndexes);
    }
}
