<?php

namespace App\ESModule\Command;

use App\ESModule\Client\ClientBuilder;
use App\ESModule\Config\ConfigGlobalFetcher;
use Elastica\Mapping;
use Elastica\Request;
use Symfony\Component\Console\Style\OutputStyle;

class IndexCommand
{
    private OutputStyle $output;

    public function __construct(
        private readonly ConfigGlobalFetcher $configGlobalFetcher,
        private readonly ClientBuilder $clientBuilder,
    ) {
    }

    public function fetchAll(): void
    {
        $this->info('Showing indexes');

        foreach ($this->configGlobalFetcher->fetch() as $connectionDto) {
            $client = $this->clientBuilder->build($connectionDto);

            $indexes = $client->getCluster()->getIndexNames();
            sort($indexes);

            $this->info('  Connection:'.$connectionDto->getName());

            foreach ($connectionDto->getIndexes() as $indexDto) {
                $index = $client->getIndex($indexDto->getNameWithPrefix());
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
            $client = $this->clientBuilder->build($connectionDto);

            $this->info('  Connection:'.$connectionDto->getName());

            $indexes = $connectionDto->getIndexes();
            foreach ($indexes as $indexDto) {
                $index = $client->getIndex($indexDto->getNameWithPrefix());
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
            $client = $this->clientBuilder->build($connectionDto);

            $this->info('  Connection:'.$connectionDto->getName().', prefix='.$connectionDto->getPrefix());

            $client->request(sprintf('%s*', $connectionDto->getPrefix()), Request::DELETE)->getStatus();
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
