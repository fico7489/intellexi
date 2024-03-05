<?php

namespace App\CQRS\Command;

class UpdateRaceCommand
{
    public function __construct(
        private readonly string $id,
        private readonly ?string $name,
        private readonly ?string $distance
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }
}
