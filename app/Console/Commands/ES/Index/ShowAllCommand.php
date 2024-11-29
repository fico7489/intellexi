<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Client\IndexClient;
use Illuminate\Console\Command;

class ShowAllCommand extends Command
{
    protected $signature = 'es:index:show-all';

    public function __construct(
        private readonly IndexClient $indexClient,
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        dd($this->indexClient->fetchAll());
    }
}
