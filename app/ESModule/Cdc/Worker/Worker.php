<?php

namespace App\ESModule\Cdc\Worker;

use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Storage\RedisStorage;
use App\ESModule\Cdc\Syncer\Syncer;

class Worker
{
    public function work()
    {
        $limit = 100;
        $sleep = 1;

        /** @var RedisStorage $storage */
        $storage = app(RedisStorage::class);

        /** @var Syncer $syncer */
        $syncer = app(Syncer::class);

        /** @var Grouper $grouper */
        $grouper = app(Grouper::class);

        while (true) {
            $payload = $storage->readCdc($limit);

            if($payload !== null){
                $dataGrouped = $grouper->group($payload);

                $syncer->sync($dataGrouped);
            }

            sleep($sleep);
        }
    }
}
