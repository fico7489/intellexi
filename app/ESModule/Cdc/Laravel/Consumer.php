<?php

namespace App\ESModule\Cdc\Laravel;

use App\ESModule\Cdc\Strategy\List\Algorithm;
use Illuminate\Console\Command;

class Consumer extends Command
{
    protected $signature = 'cdc:laravel:consume';

    public function handle()
    {
        /** @var Algorithm $algorithm */
        $algorithm = app(Algorithm::class);

        $algorithm->run();
    }
}
