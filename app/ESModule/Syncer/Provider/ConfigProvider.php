<?php

namespace App\ESModule\Syncer\Provider;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Provider\Builder\ConfigDtoBuilder;
use App\ESModule\Syncer\Provider\Builder\Dto\ConfigDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class ConfigProvider
{
    /**
     * @param array<IndexModelInterface> $configIndexes
     */
    public function __construct(
        // TODO
        private readonly DefaultConnection $configConnection,
        private readonly array $configIndexes,
        private readonly ConfigDtoBuilder $configDtoBuilder,
    ) {
    }

    public function isDatabaseNameForSync($databaseName): bool
    {
        return $databaseName === $this->getConfigDto()->getDatabaseName();
    }

    public function isTableNameForSync($tableName): bool
    {
        $syncMap = $this->getConfigDto()->getSyncMap();

        return isset($syncMap[$tableName]);
    }

    public function isTableNameForIndex($tableName): bool
    {
        $tableNamesIndex = array_flip($this->getConfigDto()->getTableNamesForSync());

        return isset($tableNamesIndex[$tableName]);
    }

    public function isTableNameClassNameOrm($tableName): bool
    {
        $tableNamesToClassNameOrmMapping = $this->getConfigDto()->getTableNamesToClassNameOrmMapping();

        dump($tableNamesToClassNameOrmMapping, $tableName);

        return isset($tableNamesToClassNameOrmMapping[$tableName]);
    }

    public function fetchIndexByIndexName(string $indexName): IndexDto
    {
        return $this->getConfigDto()->getConnectionDto()->getIndexes()[$indexName];
    }

    public function getConfigDto(): ConfigDto
    {
        return $this->configDtoBuilder->build($this->configConnection, $this->configIndexes);
    }
}
