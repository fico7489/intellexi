<?php

namespace App\ES;

use App\ESModule\Interface\IndexDefinerModelInterface;
use App\Models\User;

class UserIndex implements IndexDefinerModelInterface
{
    public function getConnection(): string
    {
        return 'default';
    }

    public function getIndexName(): string
    {
        return 'users';
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
            'first_name' => $model->first_name,
        ];
    }
}
