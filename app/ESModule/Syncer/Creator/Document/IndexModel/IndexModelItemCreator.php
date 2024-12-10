<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Dto\IndexModelDto;
use App\ESModule\Syncer\Creator\Document\IndexModel\Models\ModelsFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;

class IndexModelItemCreator
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ModelsFetcher $modelsFetcher,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    public function create(array $indexModelDtos, CdcSyncableDto $cdcSyncableDto, $modelSource, $indexNameRelated): array
    {
        $tableNameRelated = $this->configProvider->fetchTableNameByIndexName($indexNameRelated);

        // detect $indexDto
        $indexDtoRelated = $this->configProvider->fetchIndexDtoByIndexName($indexNameRelated);

        $models = $this->modelsFetcher->fetch($cdcSyncableDto, $indexDtoRelated, $modelSource);

        foreach ($models as $model) {
            $type = $cdcSyncableDto->getType();
            if ($model !== $modelSource) {
                $type = DocumentDto::TYPE_UPSERT;
            }

            $identifierValue = $this->ormAdapter->fetchIdentifierValueFromModel($model);

            $indexModelDtos[$tableNameRelated][$identifierValue] = new IndexModelDto($indexNameRelated, $identifierValue, $type, $model);
        }

        return $indexModelDtos;
    }
}
