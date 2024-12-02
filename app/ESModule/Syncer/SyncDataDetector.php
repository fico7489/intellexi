<?php

namespace App\ESModule\Syncer;

use App\ES\ApplicationIndex;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\ConfigFetcher;
use App\Models\Application;
use App\Models\Race;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SyncDataDetector
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
    ) {
    }

    public function detect(ChangedRowGroupedDto $changedRowGrouped): array
    {
        $items = [];

        $className = $this->tableToClassName($changedRowGrouped->getTable());

        $items = $this->relatedLogic($items, $changedRowGrouped);

        foreach ($this->configFetcher->fetchIndexes() as $index) {
            if ($className === $index->getClassName()) {
                $indexName = 'prefix_'.$index->getIndexName(); // TODO prefix
                $model = $this->fetchModel($className, $changedRowGrouped);

                $items[$indexName] = $index->getData([], $model);
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

    private function relatedLogic(array $items, ChangedRowGroupedDto $changedRowGrouped): array
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
                    ];
                }
            }
        }

        foreach ($relatedSyncs as $table => $data) {
            $table = $data['table'];
            $className = $data['className'];
            $relation = $data['relation'];

            if ($table === $changedRowGrouped->getTable()) {
                $model2 = $this->fetchModel($className, $changedRowGrouped);

                // TODO if only one
                $models = $model2->{$relation};

                foreach ($models as $model) {
                    // TODO detect index
                    $index = app(ApplicationIndex::class);

                    $indexName = 'prefix_'.$index->getIndexName();
                    $items[$indexName] = $index->getData([], $model);
                }
            }
        }

        return $items;
    }
}
