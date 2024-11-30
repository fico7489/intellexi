<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Syncer\Dto\ChangedDbRow;

class GeneralConverter implements ConverterInterface
{
    public function convert(string $data): ChangedDbRow
    {
        $data = json_decode($data, true);

        $database = $data['database'];
        $table = $data['table'];
        $type = $data['type'];
        $identifier = $data['identifier'];
        $changedFields = $data['changedFields'];

        return new ChangedDbRow($database, $table, $type, $identifier, $changedFields);
    }
}
