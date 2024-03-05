<?php

namespace App\CQRS\Application\Command;

class CreateApplicationCommand
{
    public function __construct(
        private readonly ?string $firstName,
        private readonly ?string $lastName,
        private readonly ?string $club,
        private readonly ?string $raceId,
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getClub(): ?string
    {
        return $this->club;
    }

    public function getRaceId(): ?string
    {
        return $this->raceId;
    }
}
