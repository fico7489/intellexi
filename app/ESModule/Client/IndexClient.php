<?php

namespace App\ESModule\Client;

use Elastica\Client;
use Elastica\Mapping;
use Elastica\Request;

class IndexClient
{
    private Client $client;

    public function __construct()
    {
        $params = [
            'host' => 'elasticsearch',
            'port' => 9200,
        ];

        $client = new Client($params);

        $this->client = $client;
    }

    public function fetchAll(): array
    {
        $indexes = $this->client->getCluster()->getIndexNames();

        sort($indexes);

        return $indexes;
    }

    public function createAll($indexNames)
    {
        foreach ($indexNames as $indexName) {
            $index = $this->client->getIndex($indexName);
            if (!$index->exists()) {
                $mapping = [];
                $settings = [];

                $index->create($settings);

                $mappingObject = new Mapping();
                $mappingObject->setProperties($mapping);
                $mappingObject->send($index);
            }
        }
    }

    public function deleteAll(string $prefix){
        $status = $this->client->request(sprintf('%s*', $prefix), Request::DELETE)->getStatus();

        dd($status);
    }
}
