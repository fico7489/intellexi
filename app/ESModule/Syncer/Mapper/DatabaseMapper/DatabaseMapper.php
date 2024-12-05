<?php

namespace App\ESModule\Syncer\Mapper\DatabaseMapper;

use Illuminate\Support\Facades\DB;

class DatabaseMapper
{
    public function fetchTableNames(): array
    {
        $tableNames = collect(DB::connection()->select('show tables'))->map(function ($val) {
            foreach ($val as $key => $tbl) {
                return $tbl;
            }
        })->toArray();

        return $tableNames;
    }

    public function fetchTableNamesToPrimaryKeysMapping(): array
    {
        $tableNames = $this->fetchTableNames();

        $tablePrimaryKeysMapping = [];
        foreach ($tableNames as $tableName) {
            $primaryKeyObjects = DB::connection()->select('SHOW KEYS FROM '.$tableName." WHERE Key_name = 'PRIMARY'");

            $primaryKey = $primaryKeyObjects[0]->Column_name;

            $tablePrimaryKeysMapping[$tableName] = $primaryKey;
        }

        return $tablePrimaryKeysMapping;
    }

    public function fetchTableNamesWithColumnsMapping(): array
    {
        $tableNamesWithColumnsMapping = [];
        $tableNames = $this->fetchTableNames();

        foreach ($tableNames as $tableName) {
            // TODO intellexi
            $columns = DB::connection()->select('SHOW COLUMNS FROM `'.$tableName.'` FROM `intellexi`;');

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
