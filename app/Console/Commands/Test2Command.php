<?php

namespace App\Console\Commands;

use App\ESModule\UpdatingMapFetcher;
use Illuminate\Console\Command;

class Test2Command extends Command
{
    protected $signature = 'test2';

    public function handle()
    {
        /** @var UpdatingMapFetcher $service */
        $service = app(UpdatingMapFetcher::class);

        $updatingMap = $service->generate();
    }
}
