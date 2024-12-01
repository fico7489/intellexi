<?php

namespace App\ESModule\Cdc\Consumer;

use App\ESModule\Cdc\Worker\Worker;
use Illuminate\Console\Command;

class RedisConsumer extends Command
{
    protected $signature = 'cdc:consumer:redis-subscribe {channel}';

    public function handle()
    {
        $channel = $this->argument('channel');

        app(Worker::class)->work($channel);
    }
}
