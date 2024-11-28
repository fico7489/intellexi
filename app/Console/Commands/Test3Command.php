<?php

namespace App\Console\Commands;

use Elastica\Client;
use Illuminate\Console\Command;

class Test3Command extends Command
{
    protected $signature = 'test3';

    public function handle()
    {
        $params = [
            'host' => 'elasticsearch',
            'port' => 9200,
        ];

        $client = new Client($params);

        try {
            $indexes = $client->getCluster()->getIndexNames();
        }catch (\Throwable $th) {
            throw $th;
            dd(get_class($th));
        }

        dd($indexes);
    }
}
