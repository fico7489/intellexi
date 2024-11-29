<?php

namespace App\ESModule\Client;

use App\ESModule\Config\Dto\ConnectionDto;
use Elastica\Client;

class ClientBuilder
{
    public function build(ConnectionDto $connectionDto): Client
    {
        $params = [
            'host' => 'elasticsearch',
            'port' => 9200,
        ];

        return new Client($params);
    }
}
