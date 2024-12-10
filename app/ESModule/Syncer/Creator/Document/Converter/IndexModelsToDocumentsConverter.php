<?php

namespace App\ESModule\Syncer\Creator\Document\Converter;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Dto\IndexModelDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;

class IndexModelsToDocumentsConverter
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
    public function convert(array $indexModelDtos): array
    {
        // TODO test grouping, add delete different
        // TODO mark document as root in DTO
        // TODO add to document source of trigger
        // TODO exclude duplicates one more time
        $documents = [];
        foreach ($indexModelDtos as $indexModelDto) {
            /** @var IndexModelDto $indexModelDto */
            $tableName = $this->configProvider->fetchTableNameByIndexName($indexModelDto->getIndexName());
            $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($tableName);
            $indexDto = $this->configProvider->fetchIndexDtoByTableName($tableName);

            $identifierValue = $indexModelDto->getIdentifierValue();
            $model = $this->ormAdapter->fetchModel($classNameOrm, $identifierValue);

            $indexNameWithPrefix = $indexDto->getNameWithPrefix();
            $data = $this->dataFetcher->fetch($indexDto, $model);
            $type = $indexModelDto->getType();
            $documents[] = new DocumentDto($indexNameWithPrefix, $identifierValue, $data, $type);
        }

        return $documents;
    }
}
