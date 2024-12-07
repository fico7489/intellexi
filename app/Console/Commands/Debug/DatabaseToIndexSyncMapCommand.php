<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Provider\ConfigProvider;
use Illuminate\Console\Command;

class DatabaseToIndexSyncMapCommand extends Command
{
    protected $signature = 'es:debug:database-to-index-sync-map';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var ConfigProvider $configProvider */
        $configProvider = app(ConfigProvider::class);
        $databaseToIndexSyncMap = $configProvider->buildSyncMapping();

        dump($databaseToIndexSyncMap);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
