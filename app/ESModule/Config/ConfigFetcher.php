<?php

namespace App\ESModule\Config;

use App\ES\Connection\DefaultConnection;
use App\ES\Index\Model\ApplicationIndex;
use App\ES\Index\Model\UserIndex;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;

class ConfigFetcher
{
    // TODO interface
    /**
     * @return array<DefaultConnection>
     */
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
        // TODO load by attributes
        return [
            app(ApplicationIndex::class),
            app(UserIndex::class),
        ];
    }
}
