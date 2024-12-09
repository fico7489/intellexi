<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\ModelMap\ModelMapCreator;
use App\ESModule\Syncer\Creator\Document\ModelMap\ModelMapFlattener;

class DocumentCreator
{
    public function __construct(
        private readonly ModelMapCreator $modelMapCreator,
        private readonly ModelMapFlattener $modelMapFlattener,
        private readonly ModelMapConverter $modelMapConverter,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $modelMapDtosGrouped = $this->modelMapCreator->create($syncItemDtos);

        $modelMapDtosFlattened = $this->modelMapFlattener->flatten($modelMapDtosGrouped);

        $documents = $this->modelMapConverter->convert($modelMapDtosFlattened);

        return $documents;
    }
}
