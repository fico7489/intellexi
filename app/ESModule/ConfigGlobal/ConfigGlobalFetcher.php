<?php

namespace App\ESModule\ConfigGlobal;

use App\ESModule\ConfigGlobal\Dto\ConfigGlobalDto;

readonly class ConfigGlobalFetcher
{
    public function fetch() : ConfigGlobalDto
    {
        return new ConfigGlobalDto(
            'elasticsearch',
            '9200',
            'test',
        );
    }
}
