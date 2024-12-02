<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\Models\Application;
use App\Models\User;

class ModelsDetector
{
    public function detectModels(ChangedRowGroupedDto $changedRowGrouped): array
    {
        $className = 'applications' === $changedRowGrouped->getTable() ? Application::class : User::class;
        $indexName = 'applications' === $changedRowGrouped->getTable() ? 'prefix_applications' : 'prefix_users';

        $model = $className::find($changedRowGrouped->getIdentifier());

        $models = [
            $indexName => $model,
        ];

        return $models;
    }
}
