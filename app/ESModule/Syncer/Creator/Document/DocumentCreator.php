<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\ModelMap\ModelMapCreator;
use App\ESModule\Syncer\Creator\Document\ModelMap\ModelMapFlattener;

class DocumentCreator
{
    public function __construct(
        private readonly ModelMapCreator $modelMapCreator,
        private readonly EsDocumentsCreator $documentsCreator,
        private readonly ModelMapFlattener $modelMapFlattener,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     */
    public function create(array $syncItemDtos): array
    {
        $modelMapDtosGrouped = $this->modelMapCreator->create($syncItemDtos);

        $modelMapDtosFlattened = $this->modelMapFlattener->flatten($modelMapDtosGrouped);

        $documents = $this->documentsCreator->create($modelMapDtosFlattened);

        return $documents;
    }
}
