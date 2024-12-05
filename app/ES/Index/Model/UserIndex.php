<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Config\SyncType\TableFetchType\TableClosureFetchType;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\Models\Application;
use App\Models\User;

class UserIndex implements IndexDefinerModelInterface
{
    public function getConnection(): string
    {
        return 'default';
    }

    public function getIndexName(): string
    {
        return 'users';
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
                'first_name'
            ],
        ]);
    }

    public function syncModels($syncModels): array
    {
        return [];
    }
}
