<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Eloquent\DatabaseMapper\DatabaseMapper;
use Illuminate\Console\Command;

class DatabaseMapperCommand extends Command
{
    protected $signature = 'es:debug:database-mapper';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var DatabaseMapper $databaseMapper */
        $databaseMapper = app(DatabaseMapper::class);

        $tableNames = $databaseMapper->fetchTableNames();

        dump($tableNames);

        $tableNamesToPrimaryKeysMapping = $databaseMapper->fetchTableNamesToPrimaryKeysMapping();
        dump($tableNamesToPrimaryKeysMapping);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
