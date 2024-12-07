<?php

namespace App\ES\Index;

use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\Models\Application;
use App\Models\User;

class ApplicationIndex implements IndexModelInterface
{
    public function getIndexName(): string
    {
        return 'applications';
    }

    public function getClassName(): string
    {
        return Application::class;
    }

    public function getData(array $data, mixed $model): array
    {
        return [
            'id' => $model->id,
            'club' => $model->club,
        ];
    }

    // TODO
    public function syncMap($syncMap): array
    {
        return array_merge($syncMap, [
            Application::class => [
                'id',
                'club',
            ],
            User::class => [
                'id',
                'first_name',
                'last_name',
            ],
            'role_user' => [
                'id',
                'role_id',
            ],
        ]);
    }

    public function syncModels($syncModels): array
    {
        return [
            Application::class => function ($model, SyncItemDto $syncItemDto, array $relatedModels) {
                return array_merge($relatedModels, [$model]);
            },
            User::class => function ($model, SyncItemDto $syncItemDto, array $relatedModels) {
                return array_merge($relatedModels, $model->applications->all());
            },
            'role_user' => function ($model, SyncItemDto $syncItemDto, array $relatedModels) {
                $user = User::find($syncItemDto->getData()['user_id']);

                return array_merge($relatedModels, [$user]);
            },
        ];
    }

    public function getMapping(array $mapping): array
    {
        // TODO resolve
        return [];

        return [
            'id' => ['type' => 'integer'],
            'club' => ['type' => 'integer'],
        ];
    }

    public function getSettings(array $settings): array
    {
        // TODO resolve
        return [];

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
}
