<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Client\IndexClient;
use Illuminate\Console\Command;

class DeleteAllCommand extends Command
{
    protected $signature = 'es:index:delete-all';

    public function __construct(
        private readonly IndexClient $indexClient,
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->indexClient->deleteAll();
    }
}
