<?php

namespace App\ES;

use App\ESModule\Interface\IndexDefinerModelInterface;
use App\Models\Application;
use App\Models\User;

class UserIndex implements IndexDefinerModelInterface
{
    public function getIndexName(): string
    {
        return 'users';
    }

    public function getClassName(): string
    {
        return User::class;
    }

    public function getConfigModel(): array
    {
        return [];
    }

    public function getMapping(array $mapping): array
    {
        return $mapping;
    }

    public function getData(array $data, mixed $model): array
    {
        return $data;
    }

    public function getUpdatingFields(): array
    {
        return [
            'id',
            'last_name',
        ];
    }

    public function getUpdatingFieldsRelated(): array
    {
        return [
            Application::class => [
                'id',
                'club',
            ],
        ];
    }
}
