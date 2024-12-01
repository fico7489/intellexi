<?php

namespace App\ESModule\Cdc\Consumer;

use App\ESModule\Cdc\Converter\MaxwellConverter;
use App\ESModule\Cdc\Job\ChangedDbRowJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisConsumer extends Command
{
    protected $signature = 'cdc:consumer:redis-subscribe {channel}';

    public function handle()
    {
        $channel = $this->argument('channel');

        while (true) {
            $payload = Redis::rpop($channel);

            if ('NULL' === gettype($payload)) {
                continue;
            } else {
                // dump($payload);

                $dto = app(MaxwellConverter::class)->convert($payload);

                // TODO
                ChangedDbRowJob::dispatchSync($dto);
            }

            sleep(0.5);
        }
    }
}
