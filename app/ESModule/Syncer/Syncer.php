<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Event\CdcChangedRowsGrouped;
use FHPlatform\Component\Config\DTO\Document;
use FHPlatform\Component\Config\DTO\Index;
use GuzzleHttp\Client;

class Syncer
{
    public function sync(CdcChangedRowsGrouped $event)
    {
        $changedRowsGrouped = $event->getChangedRowsGrouped();

        $this->dataUpdate([[
            'identifier' => 1,
            'data' => [
                'test' => 4,
            ],
        ]]);

        foreach ($changedRowsGrouped as $table => $data) {
            foreach ($data as $identifier => $changedRowGrouped) {
                /* @var ChangedRowGroupedDto $changedRowGrouped */

                dump('SYNCER:',
                    $table,
                    $identifier,
                    $changedRowGrouped,
                );
            }
        }
    }

    private function detectModels(ChangedRowGroupedDto $changedRowGrouped) : array
    {

    }

    private function documentPrepare(int $identifier, array $data): array
    {
        $indexName = 'prefix_applications';

        //TODO delete

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

    public function dataUpdate(mixed $documents): bool
    {
        $baseUri = 'http://elasticsearch:9200';

        $clientGuzzle = new Client(['base_uri' => $baseUri]);

        $datas = [];
        foreach ($documents as $document) {
            $data = $this->documentPrepare($document['identifier'], $document['data']);

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

        //TODO async

        return true;
    }
}
