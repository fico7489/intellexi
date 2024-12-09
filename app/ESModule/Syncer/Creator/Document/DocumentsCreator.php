<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncableDocument\Dto\SyncableDocumentDto;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;

class DocumentsCreator
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly DataFetcher $dataFetcher,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    /**
     * @param array<SyncableDocumentDto> $documentDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $documentDtos)
    {
        // TODO test grouping, add delete different
        // TODO mark document as root in DTO
        // TODO add to document source of trigger
        // TODO group in different service for ESAdapter
        // TODO exclude duplicates one more time
        $documentsGrouped = [];
        foreach ($documentDtos as $documentDto) {
            $indexName = $documentDto->getIndexName();
            $tableName = $this->configProvider->fetchTableNameByIndexName($indexName);

            $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($tableName);
            $identifierValue = $documentDto->getIdentifierValue();
            $type = $documentDto->getType();

            $indexDto = $this->configProvider->fetchIndexDtoByTableName($tableName);

            $model = $this->ormAdapter->fetchModel($classNameOrm, $identifierValue);

            $indexNameWithPrefix = $indexDto->getNameWithPrefix();
            $data = $this->dataFetcher->fetch($indexDto, $model);
            $documentsGrouped[$indexNameWithPrefix][] = new DocumentDto($indexNameWithPrefix, $identifierValue, $data, $type);
        }

        return $documentsGrouped;
    }
}
