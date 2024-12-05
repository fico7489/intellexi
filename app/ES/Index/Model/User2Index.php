<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\Models\User;

class User2Index implements IndexDefinerModelInterface
{
    public function getConnection(): string
    {
        return 'default';
    }

    public function getIndexName(): string
    {
        return 'users2';
    }

    public function getClassName(): string
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
