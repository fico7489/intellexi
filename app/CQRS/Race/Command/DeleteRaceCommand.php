<?php

namespace App\CQRS\Race\Command;

class DeleteRaceCommand
{
    public function __construct(
        private readonly string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
