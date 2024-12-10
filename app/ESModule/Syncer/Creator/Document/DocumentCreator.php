<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Converter\IndexModelsToDocumentsConverter;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\IndexModel\IndexModelCreator;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

class DocumentCreator
{
    public function __construct(
        private readonly IndexModelCreator $indexModelCreator,
        private readonly IndexModelsToDocumentsConverter $indexModelsToDocumentsConverter,
    ) {
    }

    /**
     * @param array<CdcSyncableDto> $syncItemDtos
     *
     * @return array<DocumentDto>
     */
    public function create(array $syncItemDtos): array
    {
        $queries = [];
        DB::listen(function (QueryExecuted $query) use (&$queries) {
            if (
                !str_contains(strtolower($query->sql), 'show')
            ) {
                $addSlashes = str_replace('?', "'?'", $query->sql);
                $querySql = vsprintf(str_replace('?', '%s', $addSlashes), $query->bindings);

                $queries[] = $querySql;
            }
        });

        $indexModelDtos = $this->indexModelCreator->create($syncItemDtos);
        dump('    count='.count($queries));
        $documentDtos = $this->indexModelsToDocumentsConverter->convert($indexModelDtos);
        dump('    count2='.count($queries));

        return $documentDtos;
    }
}
