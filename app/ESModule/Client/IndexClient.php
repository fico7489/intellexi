<?php

namespace App\ESModule\Client;

use App\ESModule\Config\ConfigGlobalFetcher;
use App\ESModule\Config\Dto\ConnectionDto;
use App\ESModule\Config\Dto\IndexDto;
use Elastica\Client;
use Elastica\Mapping;
use Elastica\Request;
use Symfony\Component\Console\Style\OutputStyle;

class IndexClient
{
    private OutputStyle $output;
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

    public function createAll(): void
    {
        $connections = $this->configGlobalFetcher->fetch();

        $this->output->writeln("<info>Creating indexes</info>");
        foreach ($connections as $connection) {
            /** @var ConnectionDto $connection */

            $this->output->writeln("<info>  Connection:" . $connection->getName(). "</info>");

            $indexes = $connection->getIndexes();
            foreach ($indexes as $indexDto) {
                /** @var IndexDto $indexDto */

                $index = $this->client->getIndex($indexDto->getNameWithPrefix());
                $exists = $index->exists();

                $this->output->writeln("<info>    Creating index:" . $indexDto->getNameWithPrefix(). ', exists:' . ($exists ? 'yes' : 'no') . "</info>");

                if (!$exists) {
                    //TODO get mapping and settings
                    $mapping = [];
                    $settings = [];

                    $index->create($settings);

                    $mappingObject = new Mapping();
                    $mappingObject->setProperties($mapping);
                    $mappingObject->send($index);
                }
            }
        }

        $this->output->writeln("<info>DONE</info>");
    }

    public function deleteAll(): void
    {
        $connections = $this->configGlobalFetcher->fetch();

        $this->output->writeln("<info>Deleting indexes</info>");
        foreach ($connections as $connection) {
            /** @var ConnectionDto $connection */

            $this->output->writeln("<info>  Connection:" . $connection->getName(). ', prefix=' . $connection->getPrefix(). "</info>");

            $prefix = $connection->getPrefix();
            $this->client->request(sprintf('%s*', $prefix), Request::DELETE)->getStatus();
        }

        $this->output->writeln("<info>DONE</info>");
    }

    public function setOutput(OutputStyle $output): void
    {
        $this->output = $output;
    }
}
