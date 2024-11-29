<?php

namespace App\ES;

use App\ESModule\Interface\IndexDefinerModelInterface;
use App\Models\Application;
use App\Models\Race;
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

    public function getConfigModel(): array
    {
        return [
            'first_name',
            'last_name',
            'race' => [
                'name',
            ],
            'user' => [
                'email',
                'userType' => [
                    'name',
                ],
                'applications' => [
                    'club',
                ],
            ],
        ];
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
        return $data;
    }

    public function getUpdatingFields(): array
    {
        return [
            'first_name',
            'last_name',
        ];
    }

    public function getUpdatingFieldsRelated(): array
    {
        return [
            User::class => [
                'email',
            ],
            Race::class => [
                'name',
            ],
            Application::class => [
                'club',
            ],
        ];
    }
}
