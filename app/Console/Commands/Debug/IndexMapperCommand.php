<?php

namespace App\Console\Commands\Debug;

use App\ESModule\Syncer\Eloquent\DatabaseMapper\DatabaseMapper;
use App\ESModule\Syncer\Eloquent\IndexMapper\IndexMapper;
use Illuminate\Console\Command;

class IndexMapperCommand extends Command
{
    protected $signature = 'es:debug:index-mapper';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        /** @var IndexMapper $indexMapper */
        $indexMapper = app(IndexMapper::class);

        $data = $indexMapper->fetchClassNamesIndex();

        dump($data);
    }
}
