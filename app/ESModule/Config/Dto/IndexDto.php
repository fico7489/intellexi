<?php

namespace App\ESModule\Config\Dto;

readonly class IndexDto
{
    public function __construct(
        private readonly string $name,
        private readonly array $mapping,
        private readonly array $settings,
        private readonly ConnectionDto $connection,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameWithPrefix(): string
    {
        return $this->getConnection()->getPrefix().$this->getName();
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getConnection(): ConnectionDto
    {
        return $this->connection;
    }
}
