<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Event\CdcChangedRowsGrouped;
use App\ESModule\Syncer\Dto\Document;
use App\Models\Application;
use App\Models\User;
use GuzzleHttp\Client;

class Syncer
{
    public function sync(CdcChangedRowsGrouped $event): void
    {
        $changedRowsGrouped = $event->getChangedRowsGrouped();

        $documents = $this->createDocuments($changedRowsGrouped);

        $this->esIndexesSync($documents);
    }

    private function esIndexesSync($dataSync): void
    {
        foreach ($dataSync as $indexName => $documents) {
            $this->esIndexSync($indexName, $documents);
        }
    }

    private function createDocuments($changedRowsGrouped): array
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

    private function documentPrepare(string $indexName, int $identifier, array $data): array
    {
        // TODO delete

        $data = ['doc' => array_merge(['id' => $identifier], $data), 'doc_as_upsert' => true];

        return [
            [
                'update' => [
                    '_index' => $indexName,
                    '_id' => $identifier,
                ],
            ],
            $data,
        ];
    }

    public function esIndexSync(string $indexName, array $documents): bool
    {
        $baseUri = 'http://elasticsearch:9200';

        $clientGuzzle = new Client(['base_uri' => $baseUri]);

        $datas = [];
        foreach ($documents as $document) {
            /** @var Document $document */
            $data = $this->documentPrepare($indexName, $document->getIdentifier(), $document->getData());

            $datas[] = $data[0];

            if (isset($data[1])) {
                $datas[] = $data[1];
            }
        }

        $documentJsons = '';
        foreach ($datas as $data) {
            $documentJsons .= json_encode($data)."\n";
        }

        if ('' === $documentJsons) {
            return true;
        }

        $documentJsons .= "\n";

        $url = '_bulk';
        $response = $clientGuzzle->request('POST', '/'.$url,
            [
                'headers' => ['Content-type' => 'application/json'],
                'body' => $documentJsons."\n",
            ]
        );

        // TODO async

        return true;
    }
}
