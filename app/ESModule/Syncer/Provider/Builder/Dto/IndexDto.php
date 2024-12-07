<?php

namespace App\ESModule\Syncer\Provider\Builder\Dto;

use App\ESModule\Config\Interface\IndexModelInterface;

readonly class IndexDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $classNameOrm,
        private readonly array $mapping,
        private readonly array $settings,
        private readonly ConnectionDto $connection,
        private readonly IndexModelInterface $definer,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassNameOrm(): string
    {
        return $this->classNameOrm;
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

    public function getDefiner(): IndexModelInterface
    {
        return $this->definer;
    }
}
