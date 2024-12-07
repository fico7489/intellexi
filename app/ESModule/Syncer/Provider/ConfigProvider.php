<?php

namespace App\ESModule\Syncer\Provider;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Provider\Builder\ConfigDtoBuilder;
use App\ESModule\Syncer\Provider\Builder\Dto\ConfigDto;
use App\ESModule\Syncer\Provider\Builder\Dto\ConnectionDto;

class ConfigProvider
{
    /**
     * @param array<IndexModelInterface> $configIndexes
     */
    public function __construct(
        // TODO
        private readonly DefaultConnection $configConnection,
        private readonly array $configIndexes,
        private readonly OrmAdapter $ormAdapter,
        private readonly ConfigDtoBuilder $configDtoBuilder,
    ) {
    }

    public function isDatabaseNameForSync($tableName): bool
    {
        // TODO
        return true;
    }

    public function isTableNameForSync($tableName): bool
    {
        $syncMap = $this->getConnectionDto()->getSyncMap();

        return isset($syncMap[$tableName]);
    }

    public function getTableNamesIndex(): array
    {
        $classNamesIndex = $this->fetchClassNamesIndex();

        $tableNamesIndex = [];
        foreach ($classNamesIndex as $className => $indexDefiner) {
            $tableName = $this->ormAdapter->convertClassNameOrmToTableName($className);

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
     * @return array<IndexModelInterface>
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

    public function fetchIndexByIndexName(string $indexName): IndexModelInterface
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

    public function getConfigDto(): ConfigDto
    {
        return $this->configDtoBuilder->build($this->configConnection, $this->configIndexes);
    }

    public function getConnectionDto(): ConnectionDto
    {
        $configDto = $this->getConfigDto();

        return $configDto->getConnectionDto();
    }
}
