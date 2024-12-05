<?php

namespace App\ESModule\Syncer\Mapper\IndexMapper;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;

class IndexMapper
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
    ) {
    }

    /**
     * @return array<IndexDefinerModelInterface>
     */
    public function fetchClassNamesIndex(): array
    {
        $indexDefiners = $this->configFetcher->fetchIndexes();

        $classNamesIndex = [];

        foreach ($indexDefiners as $indexDefiner) {
            $classNamesIndex[$indexDefiner->getClassName()] = $indexDefiner;
        }

        return $classNamesIndex;
    }

    public function fetchIndexByIndexName(string $indexName): IndexDefinerModelInterface
    {
        $classNamesIndex = $this->fetchClassNamesIndex();

        $indexDefiners = $this->configFetcher->fetchIndexes();
        foreach ($indexDefiners as $indexDefiner) {
            if ($indexDefiner->getIndexName() === $indexName) {
                return $indexDefiner;
            }
        }

        // TODO
    }
}
