<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Client\IndexClient;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;

class CreateAllCommand extends Command
{
    protected $signature = 'es:index:create-all';

    public function handle()
    {
        $indexClient = app(IndexClient::class);
        $indexClient->setOutput($this->output);

        $indexClient->createAll();
    }
}
