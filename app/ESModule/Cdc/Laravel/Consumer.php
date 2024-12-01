<?php

namespace App\ESModule\Cdc\Laravel;

use App\ESModule\Cdc\Worker\Worker;
use Illuminate\Console\Command;

class Consumer extends Command
{
    protected $signature = 'cdc:laravel:consume {channel}';

    public function handle()
    {
        $channel = $this->argument('channel');

        app(Worker::class)->work($channel);
    }
}
