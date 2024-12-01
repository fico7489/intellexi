<?php

namespace App\ESModule\Cdc\Laravel;

use App\ESModule\Cdc\Worker\Worker;
use Illuminate\Console\Command;

class Consumer extends Command
{
    protected $signature = 'cdc:laravel:consume';

    public function handle()
    {
        app(Worker::class)->work();
    }
}
