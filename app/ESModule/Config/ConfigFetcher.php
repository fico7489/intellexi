<?php

namespace App\ESModule\Config;

use App\ES\Connection\DefaultConnection;
use App\ES\Index\Model\ApplicationIndex;
use App\ES\Index\Model\UserIndex;

class ConfigFetcher
{
    public function fetchConnections(): array
    {
        return [
            app(DefaultConnection::class),
        ];
    }

    public function fetchIndexes(): array
    {
        return [
            app(ApplicationIndex::class),
            app(UserIndex::class),
        ];
    }
}
