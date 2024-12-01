<?php


namespace App\ESModule\Cdc\Storage;

use Illuminate\Support\Facades\Redis;

class RedisStorage
{
    public function readCdc() : ?string
    {
        //TODO
        $channel = 'maxwell';

        $payload = Redis::rpop($channel);

        if ('NULL' === gettype($payload)) {
            return null;
        }

        return  $payload;
    }

    public function store(string $payload) : void
    {
        $key = 'DATA';

        Redis::sadd($key, $payload);

        dump('stored', $payload);
    }

    public function readAndDelete() : array
    {
        $key = 'DATA';

        $data = Redis::smembers($key);
        Redis::del(['DATA']);

        return $data;
    }
}
