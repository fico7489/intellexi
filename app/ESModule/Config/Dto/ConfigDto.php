<?php

namespace App\ESModule\Config\Dto;

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
