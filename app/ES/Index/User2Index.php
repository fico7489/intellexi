<?php

namespace App\ES\Index;

use App\ESModule\Config\Interface\IndexModelInterface;
use App\Models\User;

class User2Index implements IndexModelInterface
{
    public function getIndexName(): string
    {
        return 'users2_es_index';
    }

    public function getClassNameOrm(): string
    {
        return User::class;
    }

    public function getMapping(array $mapping): array
    {
        return $mapping;
    }

    public function getSettings(array $settings): array
    {
        return $settings;
    }

    public function getData(array $data, mixed $model): array
    {
        return [
            'id' => $model->id,
            'last_name' => $model->last_name,
        ];
    }

    public function syncMap($syncMap): array
    {
        return [];
    }

    public function syncModels($syncModels): array
    {
        return [];
    }
}
