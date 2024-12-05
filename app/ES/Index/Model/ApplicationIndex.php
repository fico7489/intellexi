<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\SyncType\ModelFetchType\ModelClosureFetchType;
use App\ESModule\Config\SyncType\ModelFetchType\ModelRelationFetchType;
use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Config\SyncType\TableFetchType\TableClosureFetchType;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    //TODO
    public function syncMap($syncMap): array
    {
        return array_merge($syncMap, [
            Application::class => [
                'id',
                'club',
            ],
            User::class => [
                'id',
                'last_name'
            ],
            'role_user' => [
                'id',
                'role_id'
            ],
        ]);
    }

    public function syncModels($syncModels): array
    {
        return [
            User::class => function(SyncDto $syncDto, User $model, array $relatedModels) {
                return array_merge($relatedModels, $model->applications->all());
            },
            'role_user' => function(SyncDto $syncDto, $model, array $relatedModels) {
                $user = User::find($syncDto->getData()['user_id']);

                return array_merge($relatedModels, [$user]);
            },
        ];
    }

    /**
     * @return array<RelatedModelSync|RelatedTableSync>
     */
    public function getSync(): array
    {
        return [
            new RootSync(
                ['id', 'club']
            ),
            new RelatedModelSync(
                User::class,
                ['id', 'last_name'],
                new ModelRelationFetchType('applications')
            ),
            new RelatedTableSync(
                'role_user',
                ['id', 'role_id'],
                new TableClosureFetchType(function (SyncDto $syncDto): array {
                    return [
                        Application::find(2),
                    ];
                })
            ),
            /*new RelatedModelSync(
                User::class,
                ['id'],
                new ModelClosureFetchType(function (Model $model, SyncDto $syncDto): array {
                    return [Application::find(1), Application::find(17)];
                })
            ),*/
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
