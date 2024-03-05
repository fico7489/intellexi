<?php

namespace App\CQRS\Application\Query;

class ApplicationSimpleQuery
{
    public function __construct(private readonly  string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
