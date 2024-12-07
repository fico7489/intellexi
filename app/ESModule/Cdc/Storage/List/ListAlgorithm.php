<?php

namespace App\ESModule\Cdc\Storage\List;

use App\ESModule\Cdc\Processor\Processor;
use Predis\Client;

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
        $channel = 'maxwell';
        $configRedis = config('database.redis.default');
        $limit = 100;
        $sleep = 6;

        while (true) {
            $cdcPayloads = null;

            $predis = new Client($configRedis);
            $payload = $predis->blmpop(0, [$channel], 'left', 1);

            $cdcPayloads = $payload[$channel];

            if (count($cdcPayloads) < $limit) {
                usleep(2000);

                $payload = $predis->lmpop([$channel], 'left', $limit - 1);
                if ('NULL' !== gettype($payload)) {
                    $cdcPayloads2 = $payload[$channel];

                    $cdcPayloads = array_merge($cdcPayloads, $cdcPayloads2);
                }
            }

            if (null !== $cdcPayloads) {
                $this->processor->processCdcPayloads($cdcPayloads);
            }
        }
    }
}
