<?php

namespace App\Console\Commands;

use App\ESModule\Syncer\Provider\ConfigProvider;
use Illuminate\Console\Command;

class GlobalConfigCommand extends Command
{
    protected $signature = 'global:config';

    public function handle()
    {
        /** @var ConfigProvider $service */
        $service = app(ConfigProvider::class);

        $config = $service->buildConfigMap();

        dump($config);
    }
}
