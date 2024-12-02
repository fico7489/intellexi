<?php

namespace App\ESModule\CdcLaravel;

use App\ESModule\Cdc\Storage\List\ListAlgorithm;
use Illuminate\Console\Command;

class Consumer extends Command
{
    protected $signature = 'cdc:laravel:consume';

    public function handle(): void
    {
        /** @var ListAlgorithm $algorithm */
        $algorithm = app(ListAlgorithm::class);

        $algorithm->run();
    }
}
