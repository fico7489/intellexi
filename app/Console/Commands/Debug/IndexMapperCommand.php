<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Illuminate\Console\Command;

class IndexMapperCommand extends Command
{
    protected $signature = 'es:debug:index-mapper';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var SyncMapper $indexMapper */
        $indexMapper = app(SyncMapper::class);

        $data = $indexMapper->fetchClassNamesIndex();

        dump($data);
    }
}
