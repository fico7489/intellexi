<?php

namespace App\ESModule\CdcStorageRedis;

use App\ESModule\Cdc\Storage\List\Storage;
use Predis\Client;

readonly class RedisStorage implements Storage
{
    public function __construct(
        private string $channel,
        private array $options,
    ) {
    }

    public function pop(int $limit): ?array
    {
        $predis = new Client($this->options);
        $payload = $predis->lmpop([$this->channel], 'left', $limit);

        if ('NULL' === gettype($payload)) {
            return null;
        }

        return $payload[$this->channel];
    }
}
