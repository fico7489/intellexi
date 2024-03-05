<?php

namespace App\CQRS\Command;

class CreateRaceCommand
{
    public function __construct(
        private readonly string $name,
        private readonly string $distance
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDistance(): string
    {
        return $this->distance;
    }
}
