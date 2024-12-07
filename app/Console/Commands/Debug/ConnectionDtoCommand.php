<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Provider\ConfigProvider;
use Illuminate\Console\Command;

class ConnectionDtoCommand extends Command
{
    protected $signature = 'es:debug:connection-dto';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var ConfigProvider $configProvider */
        $configProvider = app(ConfigProvider::class);
        $connectionDto = $configProvider->getConnectionDto();

        dump($connectionDto);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
