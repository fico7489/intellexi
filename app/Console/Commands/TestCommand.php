<?php

namespace App\Console\Commands;

use App\ESModule\Syncer\ModelMapper;
use Elastica\Client;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class TestCommand extends Command
{
    protected $signature = 'test';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        $data = app(ModelMapper::class)->fetchAllModelClassNames();
        dump($data);

        dump(((floor(microtime(true) * 1000)) - $milliseconds) . ' ms');
    }
}
