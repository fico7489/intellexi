<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Provider\ConfigProvider;
use Illuminate\Console\Command;

class IndexMapperCommand extends Command
{
    protected $signature = 'es:debug:index-mapper';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var ConfigProvider $configProvider */
        $configProvider = app(ConfigProvider::class);

        $data = $configProvider->fetchClassNamesIndex();

        dump($data);
    }
}
