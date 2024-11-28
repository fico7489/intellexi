<?php

namespace App\Console\Commands;

use App\ES\ApplicationIndex;
use App\ESModule\ConfigModel\Converter;
use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TestCommand extends Command
{
    protected $signature = 'test';

    public function __construct(private string $test)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->testData();
        //$this->testMapping();
    }

    private function testMapping()
    {
        /** @var ApplicationIndex $index */
        $index = app(ApplicationIndex::class);

        /** @var Converter $converter */
        $converter = app(Converter::class);

        $mapping = $converter->convertMapping($index);

        dd($mapping);
    }

    private function testData()
    {
        $model = Application::find(1);

        /** @var ApplicationIndex $index */
        $index = app(ApplicationIndex::class);

        /** @var Converter $converter */
        $converter = app(Converter::class);

        $mapping = $converter->convertData($index, $model);

        dd($mapping);
    }
}
