<?php

namespace App\ES\Index;

use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\Models\User;

class UserIndex implements IndexModelInterface
{
    public function getIndexName(): string
    {
        return 'users_es_index';
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

    public function syncMap($syncMap): array
    {
        return array_merge($syncMap, [
            User::class => [
                'id',
                'first_name',
                'email',
            ],
        ]);
    }

    public function syncModels($syncModels): array
    {
        return [
            User::class => function ($model, SyncItemDto $syncItemDto, array $relatedModels) {
                return array_merge($relatedModels, [$model]);
            },
        ];
    }
}
