<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentsGrouper;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator\MatchedSyncItemCreator;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;

class DocumentsCreator
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly MatchedSyncItemCreator $matchedSyncItemCreator,
        private readonly DocumentsGrouper $documentsGrouper,
    ) {
    }

    /**
     * @param array<SyncItemDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $syncMapping = $this->configProvider->getConnectionDto()->getSyncMap();

        $documents = [];
        foreach ($syncItemDtos as $syncItemDto) {
            foreach ($syncMapping as $tableName => $indexData) {
                foreach ($indexData as $indexName => $changedFieldsTriggers) {
                    $documents = array_merge($documents, $this->matchedSyncItemCreator->createForMatched($syncItemDto, $tableName, $indexName, $changedFieldsTriggers));
                }
            }
        }

        // return  $documents;
        return $this->documentsGrouper->group($documents);
    }
}
