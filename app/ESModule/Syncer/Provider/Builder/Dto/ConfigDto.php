<?php

namespace App\ESModule\Syncer\Provider\Builder\Dto;

class ConfigDto
{
    public function __construct(
        private readonly ConnectionDto $connectionDto,
    ) {
    }

    public function getConnectionDto(): ConnectionDto
    {
        return $this->connectionDto;
    }
}
