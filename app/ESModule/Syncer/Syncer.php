<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncItem\SyncItemCreator;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;

class Syncer
{
    public function __construct(
        private readonly SyncItemCreator $syncItemCreator,
        private readonly DocumentsCreator $documentsCreator,
        private readonly SearchEngineDataClient $searchEngineSyncer,// TODO by interface
        private readonly DataFetcher $dataFetcher,
        private readonly ConfigProvider $configProvider,
    ) {
    }

    /**
     * @params array<CdcDto>
     */
    public function sync(array $cdcDtos): void
    {
        dump('count $cdcDtos='.count($cdcDtos));
        $syncItemDtos = $this->syncItemCreator->create($cdcDtos);

        dump('count $syncItemDtos='.count($syncItemDtos));
        $documentDtos = $this->documentsCreator->create($syncItemDtos);
        // dd($documentDtos);
        dump('count $documentDtos='.count($documentDtos));
        // TODO test grouping, add delete different
        // TODO mark document as root in DTO
        // TODO add to document source of trigger
        // TODO group in different service for ESAdapter
        // TODO exclude duplicates one more time
        $documentsGrouped = [];
        foreach ($documentDtos as $indexName => $data) {
            foreach ($data as $identifierValue => $data2) {
                $type = $data2['type'];
                $modelRelated = $data2['modelRelated'];

                $indexDto = $this->configProvider->fetchIndexDtoByIndexName($indexName);

                $indexNameWithPrefix = $indexDto->getNameWithPrefix();
                $data = $this->dataFetcher->fetch($indexDto, $modelRelated);
                $documentsGrouped[$indexNameWithPrefix][] = new DocumentDto($indexNameWithPrefix, $identifierValue, $data, $type);
            }
        }

        dump($documentsGrouped);
        $this->searchEngineSyncer->syncDocuments($documentsGrouped);
    }
}
