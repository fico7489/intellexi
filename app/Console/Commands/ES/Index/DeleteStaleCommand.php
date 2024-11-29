<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Command\IndexCommand;
use Illuminate\Console\Command;

class DeleteStaleCommand extends Command
{
    protected $signature = 'es:index:delete-stale';

    public function handle()
    {
        /** @var IndexCommand $indexClient */
        $indexClient = app(IndexCommand::class);
        $indexClient->setOutput($this->output);

        $indexClient->deleteStale();
    }
}
