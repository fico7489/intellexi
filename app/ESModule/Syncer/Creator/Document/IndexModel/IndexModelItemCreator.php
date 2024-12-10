<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Dto\IndexModelDto;
use App\ESModule\Syncer\Creator\Document\IndexModel\Models\ModelsFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;

class IndexModelItemCreator
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ModelsFetcher $modelsRelatedFetcher,
    ) {
    }

    public function create(array $indexModelDtos, CdcSyncableDto $cdcSyncableDto, $modelSource, $indexNameRelated): array
    {
        $tableNameRelated = $this->configProvider->fetchTableNameByIndexName($indexNameRelated);

        // detect $indexDto
        $indexDtoRelated = $this->configProvider->fetchIndexDtoByIndexName($indexNameRelated);

        $modelsRelated = $this->modelsRelatedFetcher->fetch($cdcSyncableDto, $indexDtoRelated, $modelSource);

        foreach ($modelsRelated as $modelRelated) {
            $type = $cdcSyncableDto->getType();
            if ($modelRelated !== $modelSource) {
                $type = DocumentDto::TYPE_UPSERT;
            }

            $identifierName = $modelRelated->getKeyName();
            $identifierValue = $modelRelated->{$identifierName};

            $indexModelDtos[$tableNameRelated][$identifierValue] = new IndexModelDto($indexNameRelated, $identifierValue, $type);
        }

        return $indexModelDtos;
    }
}
