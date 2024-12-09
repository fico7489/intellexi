<?php

namespace App\ESModule\Syncer\Creator\Document\ModelMap;

class ModelMapFlattener
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
