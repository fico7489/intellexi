<?php

namespace App\Console\Commands;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use Illuminate\Console\Command;

class DatabaseIndexSyncCommand extends Command
{
    protected $signature = 'es:debug:database-index-sync';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var ModelMapper $modelMapper */
        $modelMapper = app(ModelMapper::class);
        $databaseIndexSync = $modelMapper->fetchDatabaseIndexSync();

        $databaseIndexSyncThin = [];
        foreach ($databaseIndexSync as $databaseName => $databaseData) {
            foreach ($databaseData as $tableName => $tableData) {
                foreach ($tableData as $sync) {
                    /** @var IndexDefinerModelInterface $index */
                    $index = $sync['index'];

                    /** @var RootSync|RelatedTableSync|RelatedModelSync $syncType */
                    $syncType = $sync['type'];

                    $databaseIndexSyncThin[$databaseName][$tableName][] = [
                        'index' => $index->getIndexName(),
                        'type' => $syncType::class,
                    ];
                }
            }
        }

        dump($databaseIndexSyncThin);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
