<?php

namespace App\ESModule\Cdc\Converter;

interface ConverterInterface
{
    public function convert(array $payloads): array;
}
