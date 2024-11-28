<?php

namespace App\ESModule\Interface;

interface IndexInterface
{
    public function getClassName(): string;

    public function getConfigModel(): array;

    public function getMapping(array $mapping): array;

    public function getData(array $data, mixed $model): array;
}
