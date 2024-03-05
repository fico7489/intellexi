<?php

namespace App\CQRS\Query\Race;

class RaceSimpleQuery
{
    public function __construct(private readonly  string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
