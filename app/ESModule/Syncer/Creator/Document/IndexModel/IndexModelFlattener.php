<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel;

class IndexModelFlattener
{
    public function flatten(array $modelMapDtosGrouped): array
    {
        $modelMapDtosFlattened = [];
        foreach ($modelMapDtosGrouped as $tableNameRelated => $data) {
            foreach ($data as $identifierValue => $dto) {
                $modelMapDtosFlattened[] = $dto;
            }
        }

        return $modelMapDtosFlattened;
    }
}
