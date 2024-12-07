<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Provider\ConfigProvider;
use Illuminate\Console\Command;

class ConfigCommand extends Command
{
    protected $signature = 'es:debug:config';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var ConfigProvider $configProvider */
        $configProvider = app(ConfigProvider::class);
        $configDto = $configProvider->getConfigDto();

        dump($configDto);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
