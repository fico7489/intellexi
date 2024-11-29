<?php

namespace App\Console\Commands;

use App\ESModule\Config\ConfigGlobalFetcher;
use Illuminate\Console\Command;

class GlobalConfigCommand extends Command
{
    protected $signature = 'global:config';

    public function handle()
    {
        /** @var ConfigGlobalFetcher $service */
        $service = app(ConfigGlobalFetcher::class);

        $config = $service->fetch();

        dump($config);
    }
}
