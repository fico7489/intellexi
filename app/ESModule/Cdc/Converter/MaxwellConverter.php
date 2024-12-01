<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Dto\ChangedDbRow;

class MaxwellConverter implements ConverterInterface
{
    public function convert(array $payload): ChangedDbRow
    {
        $identifier = $payload['data']['id'];
        $changedFields = isset($payload['old']) ? array_keys($payload['old']) : [];

        return new ChangedDbRow(
            $payload['database'],
            $payload['table'],
            $payload['type'],
            $identifier,
            $changedFields,
            $payload['data'],
        );
    }
}
