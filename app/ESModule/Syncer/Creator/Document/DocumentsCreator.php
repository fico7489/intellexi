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
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Eloquent\DatabaseToIndexSyncMap\DatabaseToIndexSyncMapCreator;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Mapper\DatabaseMapper\DatabaseMapper;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DocumentsCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly DataFetcher $dataFetcher,
        private readonly DatabaseMapper $databaseMapper,
        private readonly DatabaseToIndexSyncMapCreator $databaseToIndexSyncMapCreator,
    ) {
    }

    /**
     * @param array<SyncDto> $syncDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncDtos): array
    {
        $documentsGrouped = [];
        foreach ($syncDtos as $syncDto) {
            $documents = $this->createForItem($syncDto);

            foreach ($documents as $document) {
                $documentsGrouped[$document->getIndex()][] = $document;
            }
        }

        return $documentsGrouped;
    }

    /**
     * @return array<DocumentDto>
     */
    public function createForItem(SyncDto $syncDto): array
    {
        $documents = [];

        $databaseToIndexSyncMap = $this->databaseToIndexSyncMapCreator->create();

        foreach ($databaseToIndexSyncMap as $tableName => $tableData) {
            foreach ($tableData as $sync) {
                /** @var IndexDefinerModelInterface $index */
                $index = $sync['index'];

                /** @var RootSync|RelatedTableSync|RelatedModelSync $type */
                $type = $sync['type'];

                // sync is matched by changed table $syncDto and table from $databaseToIndexSyncMap
                if ($tableName === $syncDto->getTableName()) {
                    $modelRoot = $this->fetchModelRoot($syncDto, $tableName);
                    $modelsRelated = $this->fetchModelsRelated($syncDto, $modelRoot, $tableName, $type);
                    $documents = $this->createDocumentsForModelsRelated($syncDto, $index, $documents, $modelsRelated, $modelRoot, $tableName);
                }
            }
        }

        return $documents;
    }

    private function fetchModelRoot(SyncDto $syncDto, string $tableName): ?Model
    {
        $classNames = $this->modelMapper->fetchAllClassNames();

        $modelRoot = null;
        if (isset($classNames[$tableName])) {
            $className = $this->modelMapper->convertTableNameToClassName($tableName);
            $modelRoot = $this->modelMapper->fetchModel($className, $syncDto);
        }

        return $modelRoot;
    }

    private function fetchModelsRelated(SyncDto $syncDto, $modelRoot, string $tableName, $type): Collection
    {
        $modelsRelated = collect();

        if ($type instanceof RootSync) {
            $modelsRelated->push($modelRoot);
        } elseif ($type instanceof RelatedModelSync) {
            if ($type->getFetchType() instanceof ModelRelationFetchType) {
                $modelsRelated = $modelRoot->{$type->getFetchType()->getRelation()};
            } elseif ($type->getFetchType() instanceof ModelClosureFetchType) {
                $modelsRelated = $type->getFetchType()->getClosure()($modelRoot, $syncDto);
            }

            $a = $modelsRelated instanceof Collection ? $modelsRelated : [$modelsRelated];
            $modelsRelated = $modelsRelated->merge($a);
        } elseif ($type instanceof RelatedTableSync) {
            if ($type->getFetchType() instanceof TableClosureFetchType) {
                $modelsRelatedItem = $type->getFetchType()->getClosure()($syncDto);
                $modelsRelated = $modelsRelated->merge($modelsRelatedItem);
            }
        } else {
            dd($type);
            // TODO exception
        }

        return $modelsRelated;
    }

    private function createDocumentsForModelsRelated(SyncDto $syncDto, IndexDefinerModelInterface $index, array $documents, Collection $modelsRelated, $modelRoot, $tableName): array
    {
        // TODO make updates unique by model->id
        foreach ($modelsRelated as $modelRelated) {
            /** @var Model $modelRelated */

            // TODO prefix
            $indexName = 'prefix_'.$index->getIndexName();

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};
            $data = $this->dataFetcher->fetch($index, $modelRelated);

            $type = DocumentDto::TYPE_UPSERT;
            if ($modelRelated === $modelRoot) {
                $type = $syncDto->getType();
            }

            $documents[] = new DocumentDto($indexName, $identifierValue, $data, $type);
        }

        return $documents;
    }
}
