<?php

namespace App\ESModule\Cdc\Storage\List;

use App\ESModule\Cdc\Processor\Processor;

readonly class ListAlgorithm
{
    public function __construct(
        private Processor $processor,
        private ListStorage $storage,
        private string $limit,
        private string $sleep,
    ) {
    }

    public function run()
    {
        while (true) {
            $cdcPayloads = $this->storage->popFromList($this->limit);

            if (null !== $cdcPayloads) {
                $this->processor->processCdcPayloads($cdcPayloads);
            }

            sleep($this->sleep);
        }
    }
}
