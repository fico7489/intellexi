<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\ConfigFetcher;
use App\Models\Application;
use App\Models\Race;
use App\Models\User;

class SyncDataDetector
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
    ) {
    }

    public function detect(ChangedRowGroupedDto $changedRowGrouped): array
    {
        $className = $this->detectClassName($changedRowGrouped->getTable());

        $items = [];
        foreach ($this->configFetcher->fetchIndexes() as $index) {
            if ($className === $index->getClassName()) {
                $indexName = 'prefix_'.$index->getIndexName(); // TODO prefix

                $model = $className::find($changedRowGrouped->getIdentifier());

                $items[$indexName] = $index->getData([], $model);
            }
        }

        return $items;
    }

    private function detectClassName($table)
    {
        // TODO
        if ('races' === $table) {
            return Race::class;
        } elseif ('users' === $table) {
            return User::class;
        } elseif ('applications' === $table) {
            return Application::class;
        }

        dd('unknown table....');
    }
}
