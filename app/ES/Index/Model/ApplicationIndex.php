<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
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
            User::class => function (SyncItemDto $syncItemDto, User $model, array $relatedModels) {
                return array_merge($relatedModels, $model->applications->all());
            },
            'role_user' => function (SyncItemDto $syncItemDto, $model, array $relatedModels) {
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
