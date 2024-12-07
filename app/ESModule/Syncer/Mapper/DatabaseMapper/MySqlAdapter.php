<?php

namespace App\ESModule\Syncer\Mapper\DatabaseMapper;

use Illuminate\Support\Facades\DB;

class MySqlAdapter
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

    public function fetchColumns(string $tableName): array
    {
        // TODO intellexi
        // TODO return columns in different keys

        return DB::connection()->select('SHOW COLUMNS FROM `'.$tableName.'` FROM `intellexi`;');
    }
}
