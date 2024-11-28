<?php

namespace App\ES;

use App\ESModule\Interface\IndexInterface;
use App\Models\Application;

class ApplicationIndex implements IndexInterface
{
    public function getClassName(): string
    {
        return Application::class;
    }

    public function getConfigModel(): array
    {
        return [
            'first_name',
            'last_name',
            'user' => [
                'email',
                'userType' => [
                    'name'
                ],
                'applications' => [
                    'club'
                ]
            ]
        ];
    }

    public function getMapping(array $mapping): array
    {
        return $mapping;
    }

    public function getData(array $data, mixed $model): array
    {
        return $data;
    }
}
