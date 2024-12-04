<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\RelatedSync\FetchType\ClosureFetchType;
use App\ESModule\Config\RelatedSync\FetchType\RelationFetchType;
use App\ESModule\Config\RelatedSync\ModelRelatedSync;
use App\ESModule\Config\RelatedSync\TableRelatedSync;
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
     * @return array<ModelRelatedSync|TableRelatedSync>
     */
    public function getRelatedSync(): array
    {
        return [
            new ModelRelatedSync(
                User::class,
                ['id', 'first_name'],
                new RelationFetchType('applications')
            ),
            new ModelRelatedSync(
                User::class,
                ['id'],
                new ClosureFetchType(function (Model $model, SyncRowDto $syncRowDto): array {
                    return [Application::find(1), Application::find(17)];
                })
            ),
        ];
    }
}
