<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\ModelMap\ModelMapCreator;

class DocumentCreator
{
    public function __construct(
        private readonly ModelMapCreator $modelMapCreator,
        private readonly EsDocumentsCreator $documentsCreator,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     */
    public function create(array $syncItemDtos): array
    {
        $modelMapDtos = $this->modelMapCreator->create($syncItemDtos);

        $modelMapDtosFlattened = [];
        foreach ($modelMapDtos as $tableNameRelated => $data) {
            foreach ($data as $identifierValue => $dto) {
                $modelMapDtosFlattened[] = $dto;
            }
        }

        $modelMapDtos = $this->documentsCreator->create($modelMapDtosFlattened);

        return $modelMapDtos;
    }
}
