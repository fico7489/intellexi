<?php

namespace App\ESModule\Syncer\Creator\Document\DocumentsCreator;

use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Creator\Document\ModelsRelated\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class DocumentCreator
{
    public function __construct(
        private readonly ModelSourceFetcher $modelSourceFetcher,
        private readonly ModelsRelatedFetcher $modelsRelatedFetcher,
        private readonly DataFetcher $dataFetcher,
    ) {
    }

    public function create(SyncItemDto $syncItemDto, IndexDto $indexDto): array
    {
        $modelSource = $this->modelSourceFetcher->fetch($syncItemDto);
        dump($syncItemDto->getTableName(), $syncItemDto->getIdentifierValue(), is_null($modelSource));

        $modelsRelated = $this->modelsRelatedFetcher->fetch($syncItemDto, $indexDto, $modelSource);

        $documents = $this->createDocumentsForModelsRelated($syncItemDto, $indexDto, $modelsRelated, $modelSource);

        return $documents;
    }

    private function createDocumentsForModelsRelated(
        SyncItemDto $syncItemDto,
        IndexDto $indexDto,
        array $modelsRelated,
        ?object $modelSource,
    ): array {
        // TODO make updates unique by model->id

        $documents = [];
        foreach ($modelsRelated as $modelRelated) {
            $indexName = $indexDto->getNameWithPrefix();

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};

            $type = $syncItemDto->getType();
            if ($modelRelated !== $modelSource) {
                $type = DocumentDto::TYPE_UPSERT;
            }

            $data = [];
            if (SyncItemDto::TYPE_UPSERT === $syncItemDto->getType()) {
                $data = $this->dataFetcher->fetch($indexDto, $modelRelated);
            }

            $documents[] = new DocumentDto($indexName, $identifierValue, $data, $type);
        }

        return $documents;
    }
}
