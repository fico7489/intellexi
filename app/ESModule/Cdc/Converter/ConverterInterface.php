<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Syncer\Dto\ChangedDbRow;

interface ConverterInterface
{
    public function convert(string $data): ChangedDbRow;
}
