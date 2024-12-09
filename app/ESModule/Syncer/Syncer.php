<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Creator\SyncDocument\Dto\SyncableDocumentDto;
use App\ESModule\Syncer\Creator\SyncDocument\SyncableDocumentsCreator;
use App\ESModule\Syncer\Creator\SyncItem\SyncableItemsCreator;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;

class Syncer
{
    public function __construct(
        private readonly SyncableItemsCreator     $syncableItemsCreator,
        private readonly SyncableDocumentsCreator $syncableDocumentsCreator,
        private readonly SearchEngineDataClient   $searchEngineSyncer,// TODO by interface
        private readonly DataFetcher              $dataFetcher,
        private readonly ConfigProvider           $configProvider,
    ) {
    }

    /**
     * @params array<CdcDto>
     */
    public function sync(array $cdcDtos): void
    {
        dump('count $cdcDtos='.count($cdcDtos));
        $syncableItemDtos = $this->syncableItemsCreator->create($cdcDtos);

        dump('count $syncableItemDtos='.count($syncableItemDtos));
        $syncableDocumentDtos = $this->syncableDocumentsCreator->create($syncableItemDtos);
dd($syncableDocumentDtos);
        dump('count $syncableDocumentDtos='.count($syncableDocumentDtos));
        // TODO test grouping, add delete different
        // TODO mark document as root in DTO
        // TODO add to document source of trigger
        // TODO group in different service for ESAdapter
        // TODO exclude duplicates one more time
        $documentsGrouped = [];
        foreach ($syncableDocumentDtos as $indexName => $data) {
            foreach ($data as $identifierValue => $data2) {
                $type = $data2['type'];
                $modelRelated = $data2['modelRelated'];

                $indexDto = $this->configProvider->fetchIndexDtoByIndexName($indexName);

                $indexNameWithPrefix = $indexDto->getNameWithPrefix();
                $data = $this->dataFetcher->fetch($indexDto, $modelRelated);
                $documentsGrouped[$indexNameWithPrefix][] = new SyncableDocumentDto($indexNameWithPrefix, $identifierValue, $data, $type);
            }
        }

        dump($documentsGrouped);
        $this->searchEngineSyncer->syncDocuments($documentsGrouped);
    }
}
