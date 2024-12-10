<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel;

class IndexModelFlattener
{
    public function flatten(array $indexModelDtosGrouped): array
    {
        $indexModelDtosFlattened = [];
        foreach ($indexModelDtosGrouped as $tableNameRelated => $data) {
            foreach ($data as $identifierValue => $dto) {
                $indexModelDtosFlattened[] = $dto;
            }
        }

        return $indexModelDtosFlattened;
    }
}
