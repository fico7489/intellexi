<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Event\CdcChangedRowsGrouped;
use App\Models\Application;
use App\Models\User;
use GuzzleHttp\Client;

class Syncer
{
    public function sync(CdcChangedRowsGrouped $event): void
    {
        $changedRowsGrouped = $event->getChangedRowsGrouped();

        $dataSync = [];
        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $changedRowGrouped) {
                /* @var ChangedRowGroupedDto $changedRowGrouped */

                $dataSync = $this->getDataSync($dataSync, $changedRowGrouped);
            }
        }

        foreach ($dataSync as $indexName => $data) {
            $this->dataUpdate($indexName, $data);
        }
    }

    private function getDataSync(array $dataSync, ChangedRowGroupedDto $changedRowGrouped): array
    {
        $models = $this->detectModels($changedRowGrouped);

        foreach ($models as $indexName => $model) {
            $dataSync[$indexName][] = [
                'identifier' => $changedRowGrouped->getIdentifier(),
                'data' => $changedRowGrouped->getData(),
            ];
        }

        return $dataSync;
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

    public function dataUpdate(string $indexName, mixed $documents): bool
    {
        $baseUri = 'http://elasticsearch:9200';

        $clientGuzzle = new Client(['base_uri' => $baseUri]);

        $datas = [];
        foreach ($documents as $document) {
            $data = $this->documentPrepare($indexName, $document['identifier'], $document['data']);

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

        dump($response->getStatusCode());

        // TODO async

        return true;
    }
}
