<?php

namespace App\ESModule\Syncer\SearchEngine;

use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use GuzzleHttp\Client;

class SearchEngineDataClient
{
    /**
     * @param array<DocumentDto> $documents
     */
    public function syncDocuments($documents): void
    {
        $documentDtosGrouped = [];
        foreach ($documents as $document) {
            $documentDtosGrouped[$document->getIndexName()][] = $document;
        }

        foreach ($documentDtosGrouped as $indexName => $documents) {
            $this->esIndexSync($indexName, $documents);
        }
    }

    public function esIndexSync(string $indexName, array $documents): bool
    {
        $baseUri = 'http://elasticsearch:9200';

        $clientGuzzle = new Client(['base_uri' => $baseUri]);

        $datas = [];
        foreach ($documents as $document) {
            /* @var DocumentDto $document */
            dump('SYNC:'.$document->getIndexName().' - '.$document->getIdentifierValue());

            $data = $this->documentPrepare($indexName, $document->getIdentifierValue(), $document->getData());

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

        // dump('status code:'.$response->getStatusCode());

        // TODO async

        return true;
    }

    private function documentPrepare(string $indexName, mixed $identifierValue, array $data): array
    {
        // TODO delete

        $data = ['doc' => array_merge(['id' => $identifierValue], $data), 'doc_as_upsert' => true];

        return [
            [
                'update' => [
                    '_index' => $indexName,
                    '_id' => $identifierValue,
                ],
            ],
            $data,
        ];
    }
}
