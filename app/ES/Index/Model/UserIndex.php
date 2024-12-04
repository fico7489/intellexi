<?php

namespace App\ES\Index\Model;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\RelatedSync\FetchType\ClosureFetchType;
use App\ESModule\Config\RelatedSync\TableRelatedSync;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;
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

    public function getUpdatingFields(): array
    {
        return [
            'id',
        ];
    }

    public function getRelatedSync(): array
    {
        return [
            new TableRelatedSync(
                'role_user',
                ['id'],
                new ClosureFetchType(function (string $tableName, SyncRowDto $syncRowDto): array {
                    dump($syncRowDto);

                    return [];
                })
            ),
        ];
    }
}
