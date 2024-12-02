<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Dto\Document;
use GuzzleHttp\Client;

class SearchEngineEsSyncer
{
    public function syncDocuments($dataSync): void
    {
        foreach ($dataSync as $indexName => $documents) {
            $this->esIndexSync($indexName, $documents);
        }
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
dump($datas);
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

        dump('status code:'.$response->getStatusCode());

        // TODO async

        return true;
    }
}
