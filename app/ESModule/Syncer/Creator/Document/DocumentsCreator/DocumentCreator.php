<?php

namespace App\ESModule\Syncer\Creator\Document\DocumentsCreator;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Creator\Document\ModelsRelated\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use Illuminate\Database\Eloquent\Model;

class DocumentCreator
{
    public function __construct(
        private readonly ModelSourceFetcher $modelSourceFetcher,
        private readonly ModelsRelatedFetcher $modelsRelatedFetcher,
        private readonly DataFetcher $dataFetcher,
    ) {
    }

    public function create(SyncItemDto $syncItemDto, IndexDefinerModelInterface $index): array
    {
        $modelSource = $this->modelSourceFetcher->fetch($syncItemDto);

        $modelsRelated = $this->modelsRelatedFetcher->fetch($syncItemDto, $index, $modelSource);

        $documents = $this->createDocumentsForModelsRelated($syncItemDto, $index, $modelsRelated, $modelSource);

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
