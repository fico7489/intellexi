<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Dto\ChangedDbRow;

interface ConverterInterface
{
    public function convert(string $payload): ChangedDbRow;
}
