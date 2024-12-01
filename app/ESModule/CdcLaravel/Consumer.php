<?php

namespace App\ESModule\CdcLaravel;

use App\ESModule\Cdc\Storage\List\Algorithm;
use Illuminate\Console\Command;

class Consumer extends Command
{
    protected $signature = 'cdc:laravel:consume';

    public function handle(): void
    {
        /** @var Algorithm $algorithm */
        $algorithm = app(Algorithm::class);

        $algorithm->run();
    }
}
