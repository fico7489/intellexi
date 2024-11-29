<?php

namespace App\ESModule\Command;

use App\ESModule\Config\ConfigGlobalFetcher;
use Elastica\Client;
use Elastica\Mapping;
use Elastica\Request;
use Symfony\Component\Console\Style\OutputStyle;

class IndexCommand
{
    private OutputStyle $output;
    private Client $client;

    public function __construct(
        private readonly ConfigGlobalFetcher $configGlobalFetcher,
    ) {
        $params = [
            'host' => 'elasticsearch',
            'port' => 9200,
        ];

        $client = new Client($params);

        $this->client = $client;
    }

    public function fetchAll(): void
    {
        $indexes = $this->client->getCluster()->getIndexNames();

        sort($indexes);

        $this->info('Showing indexes');

        foreach ($this->configGlobalFetcher->fetch() as $connectionDto) {
            $this->info('  Connection:'.$connectionDto->getName());

            foreach ($connectionDto->getIndexes() as $indexDto) {
                $index = $this->client->getIndex($indexDto->getNameWithPrefix());
                $exists = $index->exists();

                $this->info('    Index:'.$indexDto->getNameWithPrefix().', exists:'.($exists ? 'yes' : 'no'));
            }
        }

        $this->info('DONE');
    }

    public function createAll(): void
    {
        $this->info('Creating indexes');

        foreach ($this->configGlobalFetcher->fetch() as $connectionDto) {
            $this->info('  Connection:'.$connectionDto->getName());

            $indexes = $connectionDto->getIndexes();
            foreach ($indexes as $indexDto) {
                $index = $this->client->getIndex($indexDto->getNameWithPrefix());
                $exists = $index->exists();

                $this->info('    Index:'.$indexDto->getNameWithPrefix().', exists:'.($exists ? 'yes' : 'no'));

                if (!$exists) {
                    // TODO get mapping and settings
                    $mapping = [];
                    $settings = [];

                    $index->create($settings);

                    $mappingObject = new Mapping();
                    $mappingObject->setProperties($mapping);
                    $mappingObject->send($index);
                }
            }
        }

        $this->info('DONE');
    }

    public function deleteAll(): void
    {
        $this->info('Deleting indexes');

        foreach ($this->configGlobalFetcher->fetch() as $connectionDto) {
            $this->info('  Connection:'.$connectionDto->getName().', prefix='.$connectionDto->getPrefix());

            $this->client->request(sprintf('%s*', $connectionDto->getPrefix()), Request::DELETE)->getStatus();
        }

        $this->info('DONE');
    }

    public function setOutput(OutputStyle $output): void
    {
        $this->output = $output;
    }

    private function info(string $string): void
    {
        if ($this->output) {
            $this->output->writeln('<info>'.$string.'</info>');
        }
    }
}
