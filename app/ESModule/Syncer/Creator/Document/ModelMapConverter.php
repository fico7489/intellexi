<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;

class ModelMapConverter
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly DataFetcher $dataFetcher,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    /**
     * @return array<DocumentDto>
     */
    public function convert(array $modelMapDtos): array
    {
        // TODO test grouping, add delete different
        // TODO mark document as root in DTO
        // TODO add to document source of trigger
        // TODO exclude duplicates one more time
        $documents = [];
        foreach ($modelMapDtos as $modelMapDto) {
            $indexName = $modelMapDto['indexName'];
            $identifierValue = $modelMapDto['identifierValue'];
            $type = $modelMapDto['type'];

            $tableName = $this->configProvider->fetchTableNameByIndexName($indexName);
            $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($tableName);
            $indexDto = $this->configProvider->fetchIndexDtoByTableName($tableName);

            $model = $this->ormAdapter->fetchModel($classNameOrm, $identifierValue);

            $indexNameWithPrefix = $indexDto->getNameWithPrefix();
            $data = $this->dataFetcher->fetch($indexDto, $model);
            $documents[] = new DocumentDto($indexNameWithPrefix, $identifierValue, $data, $type);
        }

        return $documents;
    }
}
