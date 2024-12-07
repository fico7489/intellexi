<?php

namespace App\ESModule\Syncer\SearchEngine;

use App\ESModule\Syncer\Provider\Builder\Dto\ConnectionDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;
use Elastica\Client;
use Elastica\Mapping;
use Elastica\Request;

class SearchEngineIndexClient
{
    public function getClient(ConnectionDto $connectionDto): Client
    {
        $params = [
            'host' => $connectionDto->getHost(),
            'port' => $connectionDto->getPort(),
        ];

        return new Client($params);
    }

    public function getIndexes(ConnectionDto $connectionDto): array
    {
        $client = $this->getClient($connectionDto);

        $indexes = $client->getCluster()->getIndexNames();
        sort($indexes);

        return $indexes;
    }

    public function getIndexesByPrefix(ConnectionDto $connectionDto): array
    {
        $indexNames = $this->getIndexes($connectionDto);

        $indexesByPrefix = [];
        foreach ($indexNames as $indexName) {
            if (str_contains($indexName, $connectionDto->getPrefix())) {
                $indexesByPrefix[] = $indexName;
            }
        }

        return $indexesByPrefix;
    }

    public function indexExists(IndexDto $indexDto): bool
    {
        $client = $this->getClient($indexDto->getConnection());

        $index = $client->getIndex($indexDto->getNameWithPrefix());

        return $index->exists();
    }

    public function createIndex(IndexDto $indexDto): void
    {
        $client = $this->getClient($indexDto->getConnection());

        $index = $client->getIndex($indexDto->getNameWithPrefix());

        $index->create($indexDto->getSettings());

        $mappingObject = new Mapping();
        $mappingObject->setProperties($indexDto->getMapping());
        $mappingObject->send($index);
    }

    public function deleteByPrefix(ConnectionDto $connectionDto): void
    {
        $client = $this->getClient($connectionDto);

        $client->request(sprintf('%s*', $connectionDto->getPrefix()), Request::DELETE)->getStatus();
    }

    public function deleteByName(ConnectionDto $connectionDto, string $indexName): void
    {
        $client = $this->getClient($connectionDto);

        $index = $client->getIndex($indexName);

        $index->delete();
    }
}
