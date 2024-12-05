<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Illuminate\Console\Command;

class DatabaseToIndexSyncMapCommand extends Command
{
    protected $signature = 'es:debug:database-to-index-sync-map';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var SyncMapper $databaseToIndexSyncMapCreator */
        $databaseToIndexSyncMapCreator = app(SyncMapper::class);
        $databaseToIndexSyncMap = $databaseToIndexSyncMapCreator->create();

        $databaseToIndexSyncMapThin = [];
        foreach ($databaseToIndexSyncMap as $tableName => $tableData) {
            foreach ($tableData as $sync) {
                /** @var IndexDefinerModelInterface $index */
                $index = $sync['index'];

                /** @var RootSync|RelatedTableSync|RelatedModelSync $syncType */
                $syncType = $sync['type'];

                $databaseToIndexSyncMapThin[$tableName][] = [
                    'index' => $index->getIndexName(),
                    'type' => $syncType::class,
                ];
            }
        }

        dump($databaseToIndexSyncMapThin);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
