<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Client\IndexClient;
use Illuminate\Console\Command;

class DeleteAllCommand extends Command
{
    protected $signature = 'es:index:delete-all';

    public function handle()
    {
        /** @var IndexClient $indexClient */
        $indexClient = app(IndexClient::class);
        $indexClient->setOutput($this->output);

        $indexClient->deleteAll();
    }
}
