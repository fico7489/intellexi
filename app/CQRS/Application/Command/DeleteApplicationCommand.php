<?php

namespace App\CQRS\Application\Command;

class DeleteApplicationCommand
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
