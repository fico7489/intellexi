<?php

namespace App\ESModule\Syncer\Provider\Builder;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Syncer\Provider\Builder\Dto\ConfigDto;

class ConfigDtoBuilder
{
    public function __construct(
        private readonly ConnectionDtoBuilder $connectionDtoBuilder,
    ) {
    }

    public function build(DefaultConnection $connectionDefiner, array $indexDefiners): ConfigDto
    {
        $connectionDto = $this->connectionDtoBuilder->build($connectionDefiner, $indexDefiners);

        $configDto = new ConfigDto($connectionDto);

        return $configDto;
    }
}
