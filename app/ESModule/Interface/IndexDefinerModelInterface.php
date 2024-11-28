<?php

namespace App\ESModule\Interface;

interface IndexDefinerModelInterface
{
    public function getIndexName(): string;

    public function getClassName(): string;

    public function getConfigModel(): array;

    public function getMapping(array $mapping): array;

    public function getData(array $data, mixed $model): array;

    public function getUpdatingFields(): array;

    public function getUpdatingFieldsRelated(): array;
}
