<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Command\IndexCommand;
use Illuminate\Console\Command;

class CreateAllCommand extends Command
{
    protected $signature = 'es:index:create-all';

    public function handle()
    {
        /** @var IndexCommand $indexClient */
        $indexClient = app(IndexCommand::class);
        $indexClient->setOutput($this->output);

        $indexClient->createAll();
    }
}
