<?php

namespace App\ESModule\ConfigGlobal\Dto;

readonly class ConfigGlobalDto
{
    public function __construct(
        private readonly string $host,
        private readonly string $port,
        private readonly string $prefix
    )
    {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
