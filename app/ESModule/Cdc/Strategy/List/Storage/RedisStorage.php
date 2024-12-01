<?php

namespace App\ESModule\Cdc\Strategy\List\Storage;

use Illuminate\Support\Facades\Redis;

class RedisStorage implements Storage
{
    public function readCdc(int $limit): ?array
    {
        // TODO
        $channel = 'maxwell';

        $payload = Redis::lmpop([$channel], 'left', $limit);

        if ('NULL' === gettype($payload)) {
            return null;
        }

        return $payload[$channel];
    }
}
