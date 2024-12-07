<?php

namespace App\ESModule\Syncer\Mapper\DatabaseMapper;

class DatabaseMapper
{
    public function __construct(
        private readonly MySqlAdapter $adapter,
    ) {
    }

    public function fetchTableNames(): array
    {
        return $this->adapter->fetchTableNames();
    }

    public function fetchTableNamesToPrimaryKeysMapping(): array
    {
        return $this->adapter->fetchTableNamesToPrimaryKeysMapping();
    }

    public function fetchTableNamesWithColumnsMapping(): array
    {
        $tableNamesWithColumnsMapping = [];
        $tableNames = $this->fetchTableNames();

        foreach ($tableNames as $tableName) {
            $columns = $this->adapter->fetchColumns($tableName);

            foreach ($columns as $column) {
                $field = $column->Field;
                $type = $column->Type;

                $tableNamesWithColumnsMapping[$tableName][$field] = $type;
            }
        }

        // TODO convert

        return $tableNamesWithColumnsMapping;
    }

    public function detectIdentifierValue(string $tableName, array $data): string|array
    {
        $mapping = $this->fetchTableNamesToPrimaryKeysMapping();

        $identifierName = $mapping[$tableName];

        $identifierValue = $data[$identifierName];

        return $identifierValue;
    }
}
