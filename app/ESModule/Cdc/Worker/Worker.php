<?php

namespace App\ESModule\Cdc\Worker;

use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Strategy\List\RedisAdapter;
use App\ESModule\Cdc\Syncer\SyncerDump;

class Worker
{
    public function work()
    {
        $limit = 100;
        $sleep = 1;

        /** @var RedisAdapter $storage */
        $storage = app(RedisAdapter::class);

        /** @var SyncerDump $syncer */
        $syncer = app(SyncerDump::class);

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
