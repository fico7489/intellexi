<?php

namespace App\ESModule\Config\Interface;

interface IndexModelInterface extends IndexSyncInterface
{
    public function getIndexName(): string;

    public function getClassNameOrm(): string;

    public function getMapping(array $mapping): array;

    public function getSettings(array $settings): array;

    public function getData(array $data, mixed $model): array;
}
