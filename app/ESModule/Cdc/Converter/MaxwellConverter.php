<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Dto\CdcDto;

class MaxwellConverter implements ConverterInterface
{
    /**
     * @return array<CdcDto>
     */
    public function convertCdcPayloadsToCdcDtos(array $cdcPayloads): array
    {
        $cdcDtos = [];
        foreach ($cdcPayloads as $cdcPayload) {
            $cdcPayload = json_decode($cdcPayload, true);

            $changedFields = isset($cdcPayload['old']) ? array_keys($cdcPayload['old']) : [];
            $additional = [];

            $cdcDtos[] = new CdcDto(
                $cdcPayload['database'],
                $cdcPayload['table'],
                $cdcPayload['type'],
                $cdcPayload['data'],
                $changedFields,
                $additional
            );
        }

        return $cdcDtos;
    }
}
