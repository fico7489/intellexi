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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DocumentsCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly EloquentAdapter $eloquentAdapter,
        private readonly DataFetcher $dataFetcher,
    ) {
    }

    /**
     * @param array<SyncRowDto> $syncRowDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncRowDtos): array
    {
        $documentsGrouped = [];
        foreach ($syncRowDtos as $syncRowDto) {
            $documents = $this->createForItem($syncRowDto);

            foreach ($documents as $document) {
                $documentsGrouped[$document->getIndex()][] = $document;
            }
        }

        return $documentsGrouped;
    }

    /**
     * @return array<DocumentDto>
     */
    public function createForItem(SyncRowDto $syncRowDto): array
    {
        $documents = [];

        $databaseToIndexSyncMap = $this->modelMapper->fetchDatabaseToIndexSyncMap();

        foreach ($databaseToIndexSyncMap as $databaseName => $databaseData) {
            foreach ($databaseData as $tableName => $tableData) {
                foreach ($tableData as $sync) {
                    /** @var IndexDefinerModelInterface $index */
                    $index = $sync['index'];

                    /** @var RootSync|RelatedTableSync|RelatedModelSync $type */
                    $type = $sync['type'];

                    // sync is matched by changed table $syncRowDto and table from $databaseToIndexSyncMap
                    if ($tableName === $syncRowDto->getTableName()) {
                        $modelRoot = $this->fetchModelRoot($syncRowDto, $tableName);
                        $modelsRelated = $this->fetchModelsRelated($syncRowDto, $modelRoot, $tableName, $type);
                        dump(2222, $modelsRelated);
                        $documents = $this->createDocumentsForModelsRelated($syncRowDto, $index, $documents, $modelsRelated, $modelRoot);
                    }
                }
            }
        }

        return $documents;
    }

    private function fetchModelRoot(SyncRowDto $syncRowDto, string $tableName): ?Model
    {
        $classNames = $this->modelMapper->fetchAllClassNames();

        $modelRoot = null;
        if (isset($classNames[$tableName])) {
            $className = $this->modelMapper->convertTableNameToClassName($tableName);
            $modelRoot = $this->eloquentAdapter->fetchModel($className, $syncRowDto);
        }

        return $modelRoot;
    }

    private function fetchModelsRelated(SyncRowDto $syncRowDto, $modelRoot, string $tableName, $type): Collection
    {
        $modelsRelated = collect();

        if ($type instanceof RootSync) {
            $modelsRelated->push($modelRoot);
        } elseif ($type instanceof RelatedModelSync) {
            if ($type->getFetchType() instanceof ModelRelationFetchType) {
                $modelsRelated = $modelRoot->{$type->getFetchType()->getRelation()};
            } elseif ($type->getFetchType() instanceof ModelClosureFetchType) {
                $modelsRelated = $type->getFetchType()->getClosure()($modelRoot, $syncRowDto);
            }

            $a = $modelsRelated instanceof Collection ? $modelsRelated : [$modelsRelated];
            $modelsRelated = $modelsRelated->merge($a);
        } elseif ($type instanceof RelatedTableSync) {
            if ($type->getFetchType() instanceof TableClosureFetchType) {
                $modelsRelatedItem = $type->getFetchType()->getClosure()($syncRowDto);
                $modelsRelated = $modelsRelated->merge($modelsRelatedItem);
            }
        } else {
            dd($type);
            // TODO exception
        }

        return $modelsRelated;
    }

    private function createDocumentsForModelsRelated(SyncRowDto $syncRowDto, IndexDefinerModelInterface $index, array $documents, Collection $modelsRelated, $modelRoot): array
    {
        // TODO make updates unique by model->id
        foreach ($modelsRelated as $modelRelated) {
            // TODO prefix
            $indexName = 'prefix_'.$index->getIndexName();
            $identifierValue = $this->modelMapper->detectIdentifierValue2($modelRelated);
            $data = $this->dataFetcher->fetch($index, $modelRelated);

            $type = DocumentDto::TYPE_UPSERT;
            if ($modelRelated === $modelRoot) {
                $type = $syncRowDto->getType();
            }

            $documents[] = new DocumentDto($indexName, $identifierValue, $data, $type);
        }

        return $documents;
    }
}
