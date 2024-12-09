<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\ModelMap\ModelMapConverter;
use App\ESModule\Syncer\Creator\Document\ModelMap\ModelMapCreator;

class DocumentCreator
{
    public function __construct(
        private readonly ModelMapCreator $modelMapCreator,
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
        $modelMapDtos = $this->modelMapCreator->create($syncItemDtos);

        $documentDtos = $this->modelMapConverter->convert($modelMapDtos);

        return $documentDtos;
    }
}
