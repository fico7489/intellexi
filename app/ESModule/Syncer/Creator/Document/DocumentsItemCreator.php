<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\SyncType\ModelFetchType\ModelClosureFetchType;
use App\ESModule\Config\SyncType\ModelFetchType\ModelRelationFetchType;
use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Config\SyncType\TableFetchType\TableClosureFetchType;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use Illuminate\Support\Collection;

class DocumentsItemCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly EloquentAdapter $eloquentAdapter,
        private readonly DataFetcher $dataFetcher,
    ) {
    }

    /**
     * @return array<DocumentDto>
     */
    public function create(SyncRowDto $syncRowDto): array
    {
        $documents = [];

        $updatingMap = $this->modelMapper->fetchDatabaseMapping();

        foreach ($updatingMap as $databaseName => $databaseData) {
            foreach ($databaseData as $tableName => $tableData) {
                foreach ($tableData as $sync) {
                    /** @var IndexDefinerModelInterface $index */
                    $index = $sync['index'];

                    /** @var RootSync|RelatedTableSync|RelatedModelSync $type */
                    $type = $sync['type'];

                    if ($tableName === $syncRowDto->getTableName()) {
                        $models = $this->fetchModels($syncRowDto, $tableName, $type);
                        $documents = $this->createDocumentsFromModels($syncRowDto, $tableName, $index, $documents, $models);
                    }
                }
            }
        }

        return $documents;
    }

    private function fetchModels(SyncRowDto $syncRowDto, string $tableName, $type): Collection
    {
        $models = collect();
        $classNames = $this->modelMapper->fetchAllClassNames();

        $model = null;
        if (isset($classNames[$tableName])) {
            $className = $this->modelMapper->convertTableNameToClassName($tableName);
            $model = $this->eloquentAdapter->fetchModel($className, $syncRowDto);
        }

        if ($type instanceof RootSync) {
            $models->push($model);
        } elseif ($type instanceof RelatedModelSync) {
            if ($type->getFetchType() instanceof ModelRelationFetchType) {
                $models = $model->{$type->getFetchType()->getRelation()};
            } elseif ($type->getFetchType() instanceof ModelClosureFetchType) {
                $models = $type->getFetchType()->getClosure()($model, $syncRowDto);
            }

            $a = $models instanceof Collection ? $models : [$models];
            $models->merge($a);
        } elseif ($type instanceof RelatedTableSync) {
            if ($type->getFetchType() instanceof TableClosureFetchType) {
                $models = $type->getFetchType()->getClosure()($syncRowDto);
                $models->merge($models instanceof Collection ? $models->toArray() : [$models]);
            }
        } else {
            // TODO exception
        }

        return $models;
    }

    private function createDocumentsFromModels(SyncRowDto $syncRowDto, string $tableName, IndexDefinerModelInterface $index, array $documents, Collection $models): array
    {
        // TODO make updates unique by model->id
        foreach ($models as $model) {
            // TODO prefix
            $indexName = 'prefix_'.$index->getIndexName();

            $identifierValue = $this->modelMapper->detectIdentifierValue2($model);
            $data = $this->dataFetcher->fetch($index, $model);
            $type = SyncRowDto::TYPE_DELETE === $syncRowDto->getType() ? DocumentDto::TYPE_DELETE : DocumentDto::TYPE_UPSERT;

            $document = new DocumentDto(
                $indexName,
                $identifierValue,
                $data,
                $type
            );

            $documents[] = $document;
        }

        return $documents;
    }
}
