<?php

namespace App\ESModule\Command;

use App\ESModule\Client\ClientAdapter;
use App\ESModule\Config\ConfigGlobalFetcher;
use Symfony\Component\Console\Style\OutputStyle;

class IndexCommand
{
    private OutputStyle $output;

    public function __construct(
        private readonly ConfigGlobalFetcher $configGlobalFetcher,
        private readonly ClientAdapter       $clientAdapter,
    ) {
    }

    public function fetchAll(): void
    {
        $this->info('Showing indexes');

        foreach ($this->configGlobalFetcher->fetch() as $connectionDto) {
            $indexes = $this->clientAdapter->getIndexes($connectionDto);

            $this->info('  Connection:'.$connectionDto->getName());

            foreach ($connectionDto->getIndexes() as $indexDto) {
                $exists = $this->clientAdapter->indexExists($indexDto);

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
                $exists = $this->clientAdapter->indexExists($indexDto);

                $this->info('    Index:'.$indexDto->getNameWithPrefix().', exists:'.($exists ? 'yes' : 'no'));

                if (!$exists) {
                    $this->clientAdapter->createIndex($indexDto);
                }
            }
        }

        $this->info('DONE');
    }

    public function deleteAll(): void
    {
        $this->info('Deleting indexes');

        foreach ($this->configGlobalFetcher->fetch() as $connectionDto) {
            $client = $this->clientAdapter->getClient($connectionDto);

            $this->info('  Connection:'.$connectionDto->getName().', prefix='.$connectionDto->getPrefix());

            $this->clientAdapter->deleteByPrefix($connectionDto);
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
