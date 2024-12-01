<?php

namespace App\ESModule\Cdc\Strategy\List;

use App\ESModule\Cdc\Processor\Processor;
use App\ESModule\Cdc\Strategy\List\Storage\Adapter;

readonly class Algorithm
{
    public function __construct(
        private Processor $processor,
        private Adapter $storage,
    )
    {
    }

    public function run(){
        $limit = 100;
        $sleep = 1;

        while (true) {
            $payload = $this->storage->readCdc($limit);

            if($payload !== null){
                $this->processor->process($payload);
            }

            sleep($sleep);
        }
    }
}
