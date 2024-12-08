<?php

namespace App\ESModule\Syncer\Creator\Document\DocumentsCreator;

use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\ModelsRelated\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class DocumentCreator
{
    public function __construct(
        private readonly ModelsRelatedFetcher $modelsRelatedFetcher,
    ) {
    }

    public function create(array $documents, SyncItemDto $syncItemDto, IndexDto $indexDto, $modelSource): array
    {
        $modelsRelated = $this->modelsRelatedFetcher->fetch($syncItemDto, $indexDto, $modelSource);

        // TODO make updates unique by model->id

        foreach ($modelsRelated as $modelRelated) {
            $type = $syncItemDto->getType();
            if ($modelRelated !== $modelSource) {
                $type = DocumentDto::TYPE_UPSERT;
            }

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};

            $documents[$indexDto->getName()][$identifierValue] = $type;
        }

        return $documents;
    }
}
