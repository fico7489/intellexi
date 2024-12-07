<?php

namespace App\ESModule\Command;

use App\ESModule\Client\ClientAdapter;
use App\ESModule\Syncer\Provider\ConfigProvider;
use Symfony\Component\Console\Style\OutputStyle;

class IndexCommand
{
    private OutputStyle $output;

    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ClientAdapter  $clientAdapter,
    )
    {
    }

    public function showAll(): void
    {
        $this->info('Showing indexes');

        $connectionDto = $this->configProvider->buildConfigMap();
        foreach ($connectionDto->getIndexes() as $indexDto) {
            $exists = $this->clientAdapter->indexExists($indexDto);

            $this->info('    Index:' . $indexDto->getNameWithPrefix() . ', exists:' . ($exists ? 'yes' : 'no'));
        }

        $this->info('DONE');
    }

    public function createAll(): void
    {
        $this->info('Creating indexes');

        $connectionDto = $this->configProvider->buildConfigMap();
        $indexes = $connectionDto->getIndexes();
        foreach ($indexes as $indexDto) {
            $exists = $this->clientAdapter->indexExists($indexDto);

            $this->info('    Index:' . $indexDto->getNameWithPrefix() . ', exists:' . ($exists ? 'yes' : 'no'));

            if (!$exists) {
                $this->clientAdapter->createIndex($indexDto);
            }
        }

        $this->info('DONE');
    }

    public function deleteAll(): void
    {
        $this->info('Deleting indexes');

        $connectionDto = $this->configProvider->buildConfigMap();
        $this->info('  Connection:' . $connectionDto->getName() . ', prefix=' . $connectionDto->getPrefix());

        $this->clientAdapter->deleteByPrefix($connectionDto);

        $this->info('DONE');
    }

    public function deleteStale(): void
    {
        $this->info('Deleting stale indexes');

        $connectionDto = $this->configProvider->buildConfigMap();
        $indexesByPrefix = $this->clientAdapter->getIndexesByPrefix($connectionDto);

        $indexNamesFromConfig = [];
        foreach ($connectionDto->getIndexes() as $indexDto) {
            $indexNamesFromConfig[] = $indexDto->getNameWithPrefix();
        }

        foreach ($indexesByPrefix as $indexByPrefix) {
            if (!in_array($indexByPrefix, $indexNamesFromConfig)) {
                $this->info('    Index:' . $indexByPrefix . ' is stale');

                $this->clientAdapter->deleteByName($connectionDto, $indexByPrefix);
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
            $this->output->writeln('<info>' . $string . '</info>');
        }
    }
}
