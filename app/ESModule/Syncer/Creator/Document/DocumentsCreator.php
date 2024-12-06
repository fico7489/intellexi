<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Illuminate\Database\Eloquent\Model;

class DocumentsCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly DataFetcher $dataFetcher,
        private readonly SyncMapper $syncMapper,
        private readonly IndexMapper $indexMapper,
    ) {
    }

    /**
     * @param array<SyncItemDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $documentsGrouped = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $documents = $this->createForItem($syncItemDto);

            foreach ($documents as $document) {
                $documentsGrouped[$document->getIndex()][] = $document;
            }
        }

        return $documentsGrouped;
    }

    /**
     * @return array<DocumentDto>
     */
    public function createForItem(SyncItemDto $syncItemDto): array
    {
        $documents = [];

        $syncMapping = $this->syncMapper->create();

        foreach ($syncMapping as $tableName => $indexData) {
            foreach ($indexData as $indexName => $changedFields) {
                // sync is matched by changed table $syncItemDto and table from $syncMapping
                if ($tableName === $syncItemDto->getTableName()) {
                    $index = $this->indexMapper->fetchIndexByIndexName($indexName);

                    $modelRoot = $this->fetchModelRoot($syncItemDto, $tableName);
                    $modelsRelated = $this->fetchModelsRelated($syncItemDto, $index, $modelRoot, $tableName);
                    $documents = $this->createDocumentsForModelsRelated($syncItemDto, $index, $documents, $modelsRelated, $modelRoot, $tableName);
                }
            }
        }

        return $documents;
    }

    private function fetchModelRoot(SyncItemDto $syncItemDto, string $tableName): ?Model
    {
        if (!$this->syncMapper->isTableNameForIndex($tableName)) {
            return null;
        }

        $className = $this->modelMapper->convertTableNameToClassName($tableName);
        $modelRoot = $this->modelMapper->fetchModel($className, $syncItemDto);

        return $modelRoot;
    }

    private function fetchModelsRelated(SyncItemDto $syncItemDto, IndexDefinerModelInterface $index, $modelRoot, string $tableName): array
    {
        $syncModels = $index->syncModels([]);

        if ($this->modelMapper->isTableNameModel($tableName)) {
            $className = $this->modelMapper->convertTableNameToClassName($tableName);

            if ($index->getClassName() === $className) {
                // root model
                return [$modelRoot];
            }

            if (isset($syncModels[$className])) {
                return $syncModels[$className]($modelRoot, $syncItemDto, []);
            }
        }

        if (isset($syncModels[$tableName])) {
            return $syncModels[$tableName]($modelRoot, $syncItemDto, []);
        }

        return [];
    }

    private function createDocumentsForModelsRelated(SyncItemDto $syncItemDto, IndexDefinerModelInterface $index, array $documents, array $modelsRelated, $modelRoot, $tableName): array
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
                $type = $syncItemDto->getType();
            }

            $documents[] = new DocumentDto($indexName, $identifierValue, $data, $type);
        }

        return $documents;
    }
}
