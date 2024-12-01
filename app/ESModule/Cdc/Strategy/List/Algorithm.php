<?php

namespace App\ESModule\Cdc\Strategy\List;

use App\ESModule\Cdc\Processor\Processor;
use App\ESModule\Cdc\Strategy\List\Storage\RedisAdapter;

class Algorithm
{
    public function run(){
        $limit = 100;
        $sleep = 1;

        /** @var RedisAdapter $storage */
        $storage = app(RedisAdapter::class);

        /** @var Processor $syncer */
        $syncer = app(Processor::class);

        while (true) {
            $payload = $storage->readCdc($limit);

            if($payload !== null){
                $syncer->process($payload);
            }

            sleep($sleep);
        }
    }
}
