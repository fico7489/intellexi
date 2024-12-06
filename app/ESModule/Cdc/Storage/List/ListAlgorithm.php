<?php

namespace App\ESModule\Cdc\Storage\List;

use App\ESModule\Cdc\Processor\Processor;
use Predis\Client;

readonly class ListAlgorithm
{
    public function __construct(
        private Processor   $processor,
        private ListStorage $storage,
        private string      $limit,
        private string      $sleep,
    )
    {
    }

    public function run()
    {
        $channel = 'maxwell';
        $configRedis = config('database.redis.default');
        $limit = 100;
        $sleep = 6;

        while (true) {
            $cdcPayloads = null;

            $predis = new Client($configRedis);
            $payload = $predis->blmpop(0, [$channel], 'left', $limit);

            $cdcPayloads = $payload[$channel];

            if (null !== $cdcPayloads) {
                $this->processor->processCdcPayloads($cdcPayloads);
            }
        }
    }
}
