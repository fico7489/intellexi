<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Client\IndexClient;
use Illuminate\Console\Command;

class ShowAllCommand extends Command
{
    protected $signature = 'es:index:show-all';

    public function handle()
    {
        /** @var IndexClient $indexClient */
        $indexClient = app(IndexClient::class);
        $indexClient->setOutput($this->output);

        $indexClient->fetchAll();
    }
}
