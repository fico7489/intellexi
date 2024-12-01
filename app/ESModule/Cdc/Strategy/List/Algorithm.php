<?php

namespace App\ESModule\Cdc\Strategy\List;

use App\ESModule\Cdc\Processor\Processor;
use App\ESModule\Cdc\Strategy\List\Storage\Storage;

readonly class Algorithm
{
    public function __construct(
        private Processor $processor,
        private Storage $storage,
        private string $limit,
        private string $sleep,
    ) {
    }

    public function run()
    {
        while (true) {
            $payload = $this->storage->readCdc($this->limit);

            if (null !== $payload) {
                $this->processor->process($payload);
            }

            sleep($this->sleep);
        }
    }
}
