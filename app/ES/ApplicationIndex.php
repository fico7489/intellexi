<?php

namespace App\ES;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\Models\Application;
use App\Models\User;

class ApplicationIndex implements IndexDefinerModelInterface
{
    public function getConnection(): string
    {
        return 'default';
    }

    public function getIndexName(): string
    {
        return 'applications';
    }

    public function getClassName(): string
    {
        return Application::class;
    }

    public function getMapping(array $mapping): array
    {
        return [
            'id' => ['type' => 'integer'],
            'club' => ['type' => 'integer'],
        ];
    }

    public function getSettings(array $settings): array
    {
        return [
            'settings' => [
                'mapping' => [
                    'total_fields' => [
                        'limit' => 1001,
                    ],
                    'nested_fields' => [
                        'limit' => 301,
                    ],
                ],
            ],
        ];
    }

    public function getData(array $data, mixed $model): array
    {
        return [
            'id' => $model->id,
            'club' => $model->club,
        ];
    }

    public function getSyncRelations(): array
    {
        return [
            User::class => [
                'applications' => [
                    'id',
                    'first_name',
                ],
            ],
        ];
    }
}
