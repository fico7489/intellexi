<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Creator\Document\Helper\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\Document\Helper\ModelsRelatedValidatorAndGrouper;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Illuminate\Database\Eloquent\Model;

class DocumentsCreator
{
    public function __construct(
        private readonly DataFetcher $dataFetcher,
        private readonly SyncMapper $syncMapper,
        private readonly IndexMapper $indexMapper,
        private readonly ModelSourceFetcher $modelSourceFetcher,
        private readonly ModelsRelatedFetcher $modelsRelatedFetcher,
        private readonly ShouldSyncDetector $shouldSyncDetector,
        private readonly ModelsRelatedValidatorAndGrouper $modelsRelatedValidatorAndGrouper,
    ) {
    }

    /**
     * @param array<SyncItemDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $documents = [];
        foreach ($syncItemDtos as $syncItemDto) {
            $documents = array_merge($documents, $this->createForItem($syncItemDto));
        }

        $documentsGrouped = [];
        foreach ($documents as $document) {
            $documentsGrouped[$document->getIndex()][] = $document;
        }

        // TODO exclude duplicates one more time

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
            foreach ($indexData as $indexName => $changedFieldsTriggers) {
                // sync is matched by changed table $syncItemDto and table from $syncMapping
                if ($tableName === $syncItemDto->getTableName()) {
                    if ($this->shouldSyncDetector->detect($syncItemDto->getChangedFields(), $changedFieldsTriggers)) {
                        $index = $this->indexMapper->fetchIndexByIndexName($indexName);
                        $className = $index->getClassName();

                        $modelSource = $this->modelSourceFetcher->fetch($syncItemDto);
                        $modelsRelated = $this->modelsRelatedFetcher->fetch($syncItemDto, $index, $modelSource);
                        $modelsRelated = $this->modelsRelatedValidatorAndGrouper->validateAndGroup($modelsRelated, $className);
                        $documents = array_merge($documents, $this->createDocumentsForModelsRelated($syncItemDto, $index, $modelsRelated, $modelSource));
                    }
                }
            }
        }

        return $documents;
    }

    private function createDocumentsForModelsRelated(
        SyncItemDto $syncItemDto,
        IndexDefinerModelInterface $index,
        array $modelsRelated,
        object $modelSource,
    ): array {
        // TODO make updates unique by model->id

        $documents = [];
        foreach ($modelsRelated as $modelRelated) {
            /** @var Model $modelRelated */

            // TODO prefix
            $indexName = 'prefix_'.$index->getIndexName();

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};
            $data = $this->dataFetcher->fetch($index, $modelRelated);

            $type = DocumentDto::TYPE_UPSERT;
            if ($modelRelated === $modelSource) {
                $type = $syncItemDto->getType();
            }

            $documents[] = new DocumentDto($indexName, $identifierValue, $data, $type);
        }

        return $documents;
    }
}
