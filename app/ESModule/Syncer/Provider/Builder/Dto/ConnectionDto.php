<?php

namespace App\ESModule\Syncer\Provider\Builder\Dto;

class ConnectionDto
{
    private array $indexes;

    public function __construct(
        private readonly string $name,
        private readonly string $host,
        private readonly string $port,
        private readonly string $prefix,
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
