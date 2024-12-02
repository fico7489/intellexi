<?php

namespace App\ESModule;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Interface\IndexDefinerModelInterface;
use Illuminate\Database\Eloquent\Model;

readonly class UpdatingMapFetcher
{
    public function __construct(private ConfigFetcher $indexDefinersFetcher)
    {
    }

    public function generate(): array
    {
        $indexDefiners = $this->indexDefinersFetcher->fetchIndexes();

        $updatingMap = [];
        foreach ($indexDefiners as $indexDefiner) {
            /** @var IndexDefinerModelInterface $indexDefiner */
            $indexName = $indexDefiner->getIndexName();
            $className = $indexDefiner->getClassName();

            // root
            /** @var Model $model */
            $model = (new $className());
            $table = $model->getTable();

            $updatingFields = $indexDefiner->getUpdatingFields();
            $updatingMap[$indexName][$table] = $updatingFields;

            // related
            $updatingFieldsRelated = $indexDefiner->getUpdatingFieldsRelated();
            foreach ($updatingFieldsRelated as $classNameRelated => $fieldsRelated) {
                /** @var Model $modelRelated */
                $modelRelated = (new $classNameRelated());
                $tableRelated = $modelRelated->getTable();

                $updatingMap[$indexName][$tableRelated] = $fieldsRelated;
            }
        }

        return $updatingMap;
    }
}
