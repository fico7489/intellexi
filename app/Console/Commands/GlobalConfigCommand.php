<?php

namespace App\Console\Commands;

use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Illuminate\Console\Command;

class GlobalConfigCommand extends Command
{
    protected $signature = 'global:config';

    public function handle()
    {
        /** @var SyncMapper $service */
        $service = app(SyncMapper::class);

        $config = $service->buildConfigMap();

        dump($config);
    }
}
