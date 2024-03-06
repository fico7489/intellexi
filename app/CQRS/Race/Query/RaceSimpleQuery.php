<?php

namespace App\CQRS\Race\Query;

class RaceSimpleQuery
{
    public function __construct(private readonly string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
