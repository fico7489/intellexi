<?php

namespace App\ESModule\Syncer\Eloquent\DatabaseMapper;

use Illuminate\Support\Facades\DB;

class DatabaseMapper
{
    public function fetchTableNames(): array
    {
        $tablesNames = collect(DB::connection()->select('show tables'))->map(function ($val) {
            foreach ($val as $key => $tbl) {
                return $tbl;
            }
        })->toArray();

        return $tablesNames;
    }

    public function fetchTableNamesToPrimaryKeysMapping(): array
    {
        $tablesNames = $this->fetchTableNames();

        $tablePrimaryKeysMapping = [];
        foreach ($tablesNames as $tablesName) {
            $primaryKeyObjects = DB::connection()->select('SHOW KEYS FROM '.$tablesName." WHERE Key_name = 'PRIMARY'");

            $primaryKeys = [];
            foreach ($primaryKeyObjects as $primaryKeyObject) {
                $primaryKeys[] = $primaryKeyObject->Column_name;
            }

            $tablePrimaryKeysMapping[$tablesName] = $primaryKeys;
        }

        return $tablePrimaryKeysMapping;
    }
}
