<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\ConfigFetcher;
use App\Models\Application;
use App\Models\Race;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SyncItemsFetcher
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
    ) {
    }

    public function fetch(ChangedRowGroupedDto $changedRowGrouped): array
    {
        $items = [];

        $items = $this->addRootModelForSync($items, $changedRowGrouped);

        $items = $this->addRelatedModelsForSync($items, $changedRowGrouped);

        return $items;
    }

    private function addRootModelForSync(array $items, ChangedRowGroupedDto $changedRowGrouped): array
    {
        $className = $this->tableToClassName($changedRowGrouped->getTable());

        foreach ($this->configFetcher->fetchIndexes() as $index) {
            if ($className === $index->getClassName()) {
                $indexName = 'prefix_'.$index->getIndexName(); // TODO prefix
                $model = $this->fetchModel($className, $changedRowGrouped);

                $items[$indexName] = $index->getData([], $model);
            }
        }

        return $items;
    }

    private function addRelatedModelsForSync(array $items, ChangedRowGroupedDto $changedRowGrouped): array
    {
        $relatedSyncs = [];
        foreach ($this->configFetcher->fetchIndexes() as $index) {
            $syncRelations = $index->getSyncRelations();

            foreach ($syncRelations as $classNameRelated => $data) {
                foreach ($data as $relationRelated => $changedFields) {
                    $tableRelated = $this->classNameToTable($classNameRelated);

                    $relatedSyncs[] = [
                        'table' => $tableRelated,
                        'className' => $classNameRelated,
                        'changedFields' => $changedFields,
                        'relation' => $relationRelated,
                        'index' => $index,
                    ];
                }
            }
        }

        foreach ($relatedSyncs as $table => $data) {
            $table = $data['table'];
            $className = $data['className'];
            $relation = $data['relation'];
            $index = $data['index'];

            if ($table === $changedRowGrouped->getTable()) {
                $model = $this->fetchModel($className, $changedRowGrouped);

                $models = $model->{$relation};

                $models = $models instanceof Collection ? $models : [$models];

                foreach ($models as $model) {
                    $indexName = 'prefix_'.$index->getIndexName();
                    $items[$indexName] = $index->getData([], $model);
                }
            }
        }

        return $items;
    }

    private function fetchModel(string $className, ChangedRowGroupedDto $changedRowGrouped): ?Model
    {
        return $className::find($changedRowGrouped->getIdentifier());
    }

    private function tableToClassName($table): string
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

    private function classNameToTable($className): string
    {
        // TODO
        if (Race::class === $className) {
            return 'races';
        } elseif (User::class === $className) {
            return 'users';
        } elseif (Application::class === $className) {
            return 'applications';
        }

        dd('unknown className....');
    }
}
