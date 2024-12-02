<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Syncer\Dto\Document;
use App\Models\Application;
use App\Models\User;

class DocumentsCreator
{
    public function createDocuments($changedRowsGrouped): array
    {
        $documents = [];
        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $changedRowGrouped) {
                /* @var ChangedRowGroupedDto $changedRowGrouped */
                $models = $this->detectModels($changedRowGrouped);

                foreach ($models as $indexName => $model) {
                    $documents[$indexName][] = new Document(
                        $indexName,
                        $changedRowGrouped->getIdentifier(),
                        $changedRowGrouped->getData(),
                        ChangedRowDto::TYPE_DELETE === $changedRowGrouped->getType() ? Document::TYPE_DELETE : Document::TYPE_UPSERT,
                    );
                }
            }
        }

        return $documents;
    }

    private function detectModels(ChangedRowGroupedDto $changedRowGrouped): array
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
