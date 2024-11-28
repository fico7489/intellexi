<?php

namespace App\ESModule;

use App\ESModule\Interface\IndexDefinerModelInterface;

readonly class UpdatingMapFetcher
{
    public function __construct(private IndexDefinersFetcher $indexDefinersFetcher)
    {
    }

    public function generate(): array
    {
        $indexDefiners = $this->indexDefinersFetcher->fetchAll();

        $updatingMap = [];
        foreach ($indexDefiners as $indexDefiner) {
            /** @var IndexDefinerModelInterface $indexDefiner */

            $indexName = $indexDefiner->getIndexName();
            $className = $indexDefiner->getClassName();

            $updatingFieldsRelated = $indexDefiner->getUpdatingFieldsRelated();

            foreach ($updatingFieldsRelated as $classNameRelated => $fieldsRelated) {
                $updatingMap[$classNameRelated][$indexName] = $fieldsRelated;
            }
        }

        dd($updatingMap);

        return [];
    }
}
