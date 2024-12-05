<?php

namespace App\ESModule\Syncer\Mapper\IndexMapper;

use App\ESModule\Config\ConfigFetcher;

class IndexMapper
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
    ) {
    }

    public function fetchClassNamesIndex(): array
    {
        $indexDefiners = $this->configFetcher->fetchIndexes();

        $classNamesIndex = [];

        foreach ($indexDefiners as $indexDefiner) {
            $classNamesIndex[$indexDefiner->getClassName()] = $indexDefiner;
        }

        return $classNamesIndex;
    }
}
