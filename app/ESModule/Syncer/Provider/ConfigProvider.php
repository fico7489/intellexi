<?php

namespace App\ESModule\Syncer\Provider;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Provider\Builder\ConfigDtoBuilder;
use App\ESModule\Syncer\Provider\Builder\Dto\ConfigDto;

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

    public function fetchIndexByIndexName(string $indexName): IndexModelInterface
    {
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
}
