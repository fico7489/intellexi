<?php

namespace App\ESModule\Config;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Dto\ConnectionDto;
use App\ESModule\Config\Dto\IndexDto;

readonly class ConfigGlobalFetcher
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
    )
    {
    }

    public function fetch() : array
    {
        $connectionsDefiners = $this->configFetcher->fetchConnections();
        $indexDefiners = $this->configFetcher->fetchIndexes();

        $connectionDtos = [];
        foreach ($connectionsDefiners as $connectionDefiner) {
            /** @var DefaultConnection $connectionDefiner */
            $connection = new ConnectionDto(
                $connectionDefiner->getName(),
                $connectionDefiner->getHost(),
                $connectionDefiner->getPort(),
                $connectionDefiner->getPrefix(),
            );

            $indexes = [];
            foreach ($indexDefiners as $indexDefiner) {
                if($indexDefiner->getConnection() === $connection->getName()) {
                    $indexes[] = new IndexDto(
                        $indexDefiner->getIndexName(),
                        $indexDefiner->getMapping([]),
                        $indexDefiner->getSettings([]),
                        $connection
                    );
                }
            }

            $connection->setIndexes($indexes);

            $connectionDtos[] = $connection;
        }

        return $connectionDtos;
    }
}
