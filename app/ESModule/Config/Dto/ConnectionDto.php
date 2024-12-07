<?php

namespace App\ESModule\Config\Dto;

class ConnectionDto
{
    private array $indexes;

    public function __construct(
        private readonly string $name,
        private readonly string $host,
        private readonly string $port,
        private readonly string $prefix,
        private readonly array $syncMap,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getSyncMap(): array
    {
        return $this->syncMap;
    }

    public function setIndexes(array $indexes): void
    {
        $this->indexes = $indexes;
    }

    /**
     * @return array<IndexDto>
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }
}
