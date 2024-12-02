<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Dto\CdcDto;

interface ConverterInterface
{
    /**
     * @return array<CdcDto>
     */
    public function convertCdcPayloadsToCdcDtos(array $cdcPayloads): array;
}
