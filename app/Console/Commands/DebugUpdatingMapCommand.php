<?php

namespace App\Console\Commands;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use Illuminate\Console\Command;

class DebugUpdatingMapCommand extends Command
{
    protected $signature = 'es:debug:updating-map';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var ModelMapper $modelMapper */
        $modelMapper = app(ModelMapper::class);
        $updatingMap = $modelMapper->fetchDatabaseMapping();

        $updatingMapThin = [];
        foreach ($updatingMap as $databaseName => $databaseData) {
            foreach ($databaseData as $tableName => $tableData) {
                foreach ($tableData as $sync) {
                    /** @var IndexDefinerModelInterface $index */
                    $index = $sync['index'];

                    /** @var RootSync|RelatedTableSync|RelatedModelSync $syncType */
                    $syncType = $sync['type'];

                    $updatingMapThin[$databaseName][$tableName][] = [
                        'index' => $index->getIndexName(),
                        'type' => $syncType::class,
                    ];
                }
            }
        }

        // dump($updatingMap);
        dump($updatingMapThin);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
