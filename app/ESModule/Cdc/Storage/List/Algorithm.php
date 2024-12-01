<?php

namespace App\ESModule\Cdc\Storage\List;

use App\ESModule\Cdc\Processor\Processor;

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
            $payload = $this->storage->pop($this->limit);

            if (null !== $payload) {
                $this->processor->process($payload);
            }

            sleep($this->sleep);
        }
    }
}
