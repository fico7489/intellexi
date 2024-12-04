<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\Sync\ModelFetchType\ModelClosureFetchType;
use App\ESModule\Config\Sync\ModelFetchType\ModelRelationFetchType;
use App\ESModule\Config\Sync\RelatedModelSync;
use App\ESModule\Config\Sync\RelatedTableSync;
use App\ESModule\Config\Sync\RootSync;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;
use App\Models\Application;
use App\Models\User;
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
            /*new RelatedModelSync(
                User::class,
                ['id'],
                new ModelClosureFetchType(function (Model $model, SyncRowDto $syncRowDto): array {
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
