<?php

namespace App\ESModule\Config;

use App\ES\ApplicationIndex;
use App\ES\Connection\DefaultConnection;
use App\ES\User2Index;
use App\ES\UserIndex;
use App\ESModule\Interface\IndexDefinerModelInterface;

class ConfigFetcher
{
    public function fetchConnections(): array
    {
        return [
            app(DefaultConnection::class),
        ];
    }

    /**
     * @return array<IndexDefinerModelInterface>
     */
    public function fetchIndexes(): array
    {
        return [
            app(ApplicationIndex::class),
            app(UserIndex::class),
            app(User2Index::class),
        ];
    }
}
