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
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Mapper\DatabaseMapper\DatabaseMapper;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DocumentsCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly DataFetcher $dataFetcher,
        private readonly DatabaseMapper $databaseMapper,
        private readonly SyncMapper $syncMapper,
        private readonly IndexMapper  $indexMapper,
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

        $syncMapping = $this->syncMapper->create();

        foreach ($syncMapping as $tableName => $indexData) {
            foreach ($indexData as $indexName => $changedFields) {
                // sync is matched by changed table $syncDto and table from $syncMapping
                if ($tableName === $syncDto->getTableName()) {
                    $index = $this->indexMapper->fetchIndexByIndexName($indexName);

                    $modelRoot = $this->fetchModelRoot($syncDto, $tableName);
                    $modelsRelated = $this->fetchModelsRelated($syncDto, $index, $modelRoot, $tableName);
                    $documents = $this->createDocumentsForModelsRelated($syncDto, $index, $documents, $modelsRelated, $modelRoot, $tableName);
                }
            }
        }

        return $documents;
    }

    private function fetchModelRoot(SyncDto $syncDto, string $tableName): ?Model
    {
        if (!$this->syncMapper->isTableNameForIndex($tableName)) {
            return null;
        }

        $className = $this->modelMapper->convertTableNameToClassName($tableName);
        $modelRoot = $this->modelMapper->fetchModel($className, $syncDto);

        return $modelRoot;
    }

    private function fetchModelsRelated(SyncDto $syncDto, IndexDefinerModelInterface $index, $modelRoot, string $tableName): array
    {
        $className = $this->modelMapper->convertTableNameToClassName($tableName);

        if( ! isset($index->syncModels([])[$className])){
            return [];
        }

        return $index->syncModels([])[$className]($syncDto, $modelRoot, []);
    }

    private function createDocumentsForModelsRelated(SyncDto $syncDto, IndexDefinerModelInterface $index, array $documents, array $modelsRelated, $modelRoot, $tableName): array
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
