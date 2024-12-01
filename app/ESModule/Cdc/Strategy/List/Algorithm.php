<?php

namespace App\ESModule\Cdc\Strategy\List;

use App\ESModule\Cdc\Strategy\List\Storage\RedisAdapter;
use App\ESModule\Cdc\Syncer\Syncer;

class Algorithm
{
    public function run(){
        $limit = 100;
        $sleep = 1;

        /** @var RedisAdapter $storage */
        $storage = app(RedisAdapter::class);

        /** @var Syncer $syncer */
        $syncer = app(Syncer::class);

        while (true) {
            $payload = $storage->readCdc($limit);

            if($payload !== null){
                $syncer->sync($payload);
            }

            sleep($sleep);
        }
    }
}
