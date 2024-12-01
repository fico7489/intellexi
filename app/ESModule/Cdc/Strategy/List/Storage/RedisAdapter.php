<?php

namespace App\ESModule\Cdc\Strategy\List\Storage;

use Illuminate\Support\Facades\Redis;

class RedisAdapter implements Adapter
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
