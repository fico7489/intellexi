<?php

namespace App\ESModule\Config\Interface;

interface IndexDefinerModelInterface
{
    public function getIndexName(): string;

    public function getClassName(): string;

    public function getMapping(array $mapping): array;

    public function getSettings(array $settings): array;

    public function getData(array $data, mixed $model): array;

    public function getUpdatingFields(): array;

    public function getSync(): array;
}
