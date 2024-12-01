<?php

namespace App\ESModule\Cdc\Worker;

use App\ESModule\Syncer\Dto\ChangedDbRow;
use App\ESModule\Syncer\Syncer;
use Illuminate\Support\Facades\Redis;

class Worker
{
    public function work($channel)
    {
        while (true) {
            $payload = Redis::rpop($channel);

            if ('NULL' === gettype($payload)) {
                continue;
            } else {
                $payload = json_decode($payload, true);

                $database = $payload['database'];
                $table = $payload['table'];
                $type = $payload['type'];
                $identifier = $payload['data']['id'];
                $changedFields = isset($payload['old']) ? array_keys($payload['old']) : [];
                $data = $payload['data'];

                app(Syncer::class)->sync(new ChangedDbRow(
                    $database,
                    $table,
                    $type,
                    $identifier,
                    $changedFields,
                    $data
                ));
            }

            sleep(0.5);
        }
    }
}
