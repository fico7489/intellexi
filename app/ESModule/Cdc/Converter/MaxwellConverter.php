<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Dto\ChangedDbRow;

class MaxwellConverter implements ConverterInterface
{
    public function convert(string $payload): ChangedDbRow
    {
        $payload = json_decode($payload, true);

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
