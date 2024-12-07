<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Adapter\DatabaseAdapter\DatabaseAdapter;
use Illuminate\Console\Command;

class DatabaseMapperCommand extends Command
{
    protected $signature = 'es:debug:database-mapper';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var DatabaseAdapter $databaseMapper */
        $databaseMapper = app(DatabaseAdapter::class);

        $tableNames = $databaseMapper->fetchTableNames();

        dump($tableNames);

        $tableNamesToPrimaryKeysMapping = $databaseMapper->fetchTableNamesToPrimaryKeysMapping();
        dump($tableNamesToPrimaryKeysMapping);

        $tableNamesWithColumnsMapping = $databaseMapper->fetchTableNamesWithColumnsMapping();
        dump($tableNamesWithColumnsMapping);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
