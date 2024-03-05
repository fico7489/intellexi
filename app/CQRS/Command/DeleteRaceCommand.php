<?php

namespace App\CQRS\Command;

class DeleteRaceCommand
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
}
