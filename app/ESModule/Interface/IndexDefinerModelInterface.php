<?php

namespace App\ESModule\Interface;

interface IndexDefinerModelInterface
{
    public function getIndexName(): string;

    public function getClassName(): string;

    public function getMapping(array $mapping): array;

    public function getSettings(array $settings): array;

    public function getData(array $data, mixed $model): array;
}
