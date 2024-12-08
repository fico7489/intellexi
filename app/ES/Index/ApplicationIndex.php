<?php

namespace App\ES\Index;

use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\Models\Application;
use App\Models\Role;
use App\Models\User;

class ApplicationIndex implements IndexModelInterface
{
    public function getIndexName(): string
    {
        return 'applications_es_index';
    }

    public function getClassNameOrm(): string
    {
        return Application::class;
    }

    public function getData(array $data, mixed $model): array
    {
        /** @var Application $model */
        $data = [
            'id' => $model->id,
            'club' => $model->club,
        ];

        if ($model->user) {
            $user = $model->user;

            $userData = [
                'id' => $user->id,
                'first_name' => $user->first_name,
            ];

            $roles = [];
            foreach ($model->user->roles as $role) {
                $roles[] = $role->name;
            }
            $userData['roles'] = $roles;

            $data['user'] = $userData;
        }

        return $data;
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
            Role::class => [
                'name',
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
            Role::class => function (object $model, SyncItemDto $syncItemDto, array $relatedModels) {
                $users = $model->users;

                $applications = [];
                foreach ($users as $user) {
                    $applications = array_merge($applications, $user->applications->all());
                }

                return array_merge($relatedModels, $applications);
            },
            'role_user' => function ($model, SyncItemDto $syncItemDto, array $relatedModels) {
                $user = User::find($syncItemDto->getData()['user_id']);

                if (!$user) {
                    return $relatedModels;
                }
                dump(2222);

                return array_merge($relatedModels, $user->applications->all());
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
