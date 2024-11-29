<?php

namespace App\ESModule\Client;

use App\ESModule\Config\ConfigGlobalFetcher;
use App\ESModule\Config\Dto\ConnectionDto;
use App\ESModule\Config\Dto\IndexDto;
use Elastica\Client;
use Elastica\Mapping;
use Elastica\Request;

class IndexClient
{
    private Client $client;

    public function __construct(
        private readonly ConfigGlobalFetcher $configGlobalFetcher,
    )
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
        $connections = $this->configGlobalFetcher->fetch();

        foreach ($connections as $connection) {
            /** @var ConnectionDto $connection */

            $indexes = $connection->getIndexes();
            foreach ($indexes as $indexDto) {
                /** @var IndexDto $indexDto */

                $index = $this->client->getIndex($indexDto->getNameWithPrefix());
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
    }

    public function deleteAll(string $prefix){
        $connections = $this->configGlobalFetcher->fetch();

        foreach ($connections as $connection) {
            /** @var ConnectionDto $connection */

            $prefix = $connection->getPrefix();
            $status = $this->client->request(sprintf('%s*', $prefix), Request::DELETE)->getStatus();
            dd($status);

            $indexes = $connection->getIndexes();

            foreach ($indexes as $index) {
                //
            }
        }
    }
}
