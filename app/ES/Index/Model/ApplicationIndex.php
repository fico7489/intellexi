<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\Related\SyncRelation;
use App\ESModule\Config\Related\Type\ModelClosureType;
use App\ESModule\Config\Related\Type\RelationType;
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

    public function getData(array $data, mixed $model): array
    {
        return [
            'id' => $model->id,
            'club' => $model->club,
        ];
    }

    public function getUpdatingFields(): array
    {
        return [
            'id',
            'club',
        ];
    }

    /**
     * @return array<SyncRelation>
     */
    public function getSyncRelations(): array
    {
        return [
            new SyncRelation(
                User::class,
                [
                    'id',
                    'first_name',
                ],
                new RelationType('applications')
            ),
            new SyncRelation(
                User::class,
                ['id'],
                new ModelClosureType(function (Model $model, SyncRowDto $syncRowDto): array {
                    return [
                        Application::find(1),
                        Application::find(17),
                    ];
                })
            ),
        ];
    }
}
