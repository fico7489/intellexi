<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Dto\ChangedRowDto;

class MaxwellConverter implements ConverterInterface
{
    public function convert(array $payloads): array
    {
        $changedRowsDtos = [];
        foreach ($payloads as $payload) {
            $payload = json_decode($payload, true);

            $identifier = $payload['data']['id'];
            $changedFields = isset($payload['old']) ? array_keys($payload['old']) : [];

            $changedRowsDtos[]= new ChangedRowDto(
                $payload['database'],
                $payload['table'],
                $payload['type'],
                $identifier,
                $changedFields,
                $payload['data'],
            );
        }

        return $changedRowsDtos;
    }
}
