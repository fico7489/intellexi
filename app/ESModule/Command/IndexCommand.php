<?php

namespace App\ESModule\Command;

use App\ESModule\Client\ClientAdapter;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Symfony\Component\Console\Style\OutputStyle;

class IndexCommand
{
    private OutputStyle $output;

    public function __construct(
        private readonly SyncMapper $syncMapper,
        private readonly ClientAdapter $clientAdapter,
    ) {
    }

    public function showAll(): void
    {
        $this->info('Showing indexes');

        foreach ($this->syncMapper->buildConfigMap() as $connectionDto) {
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

        foreach ($this->syncMapper->buildConfigMap() as $connectionDto) {
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

        foreach ($this->syncMapper->buildConfigMap() as $connectionDto) {
            $client = $this->clientAdapter->getClient($connectionDto);

            $this->info('  Connection:'.$connectionDto->getName().', prefix='.$connectionDto->getPrefix());

            $this->clientAdapter->deleteByPrefix($connectionDto);
        }

        $this->info('DONE');
    }

    public function deleteStale(): void
    {
        $this->info('Deleting stale indexes');

        foreach ($this->syncMapper->buildConfigMap() as $connectionDto) {
            $indexesByPrefix = $this->clientAdapter->getIndexesByPrefix($connectionDto);

            $this->info('  Connection:'.$connectionDto->getName().', prefix='.$connectionDto->getPrefix());

            $indexNamesFromConfig = [];
            foreach ($connectionDto->getIndexes() as $indexDto) {
                $indexNamesFromConfig[] = $indexDto->getNameWithPrefix();
            }

            foreach ($indexesByPrefix as $indexByPrefix) {
                if (!in_array($indexByPrefix, $indexNamesFromConfig)) {
                    $this->info('    Index:'.$indexByPrefix.' is stale');

                    $this->clientAdapter->deleteByName($connectionDto, $indexByPrefix);
                }
            }
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
