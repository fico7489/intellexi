<?php

namespace App\Console\Commands;

use App\ES\ApplicationIndex;
use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestCommand extends Command
{
    protected $signature = 'test';

    public function __construct(private string $test)
    {
        parent::__construct();
    }

    public function handle()
    {
        //$this->testData();
        $this->testMapping();
    }

    private function testMapping()
    {
        $model = Application::find(1);

        /** @var ApplicationIndex $applicationIndex */
        $applicationIndex = app(ApplicationIndex::class);

        $data = $applicationIndex->getData();
        $className = $applicationIndex->getClassName();

        $mapping = $this->fetchMapping($data, new $className);

        dd($mapping);
    }

    private function fetchMapping(array $data, Model $model): array
    {
        $mapping = [];
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $mapping[$value] = ['type' => 'string'];
            } else {
                /** @var BelongsTo $relation */
                $relation = (new $model())->{$key}();

                $type = $relation instanceof BelongsTo ? 'object' : 'nested';
                $related = $relation->getRelated();

                $mapping[$key] = [
                    'type' => $type,
                    'properties' => $this->fetchMapping($value, $related),
                ];
            }
        }

        return $mapping;
    }

    private function testData()
    {
        $model = Application::find(1);

        $data = app(ApplicationIndex::class)->getData();

        $modelData = $this->fetchData($data, $model);

        dd($modelData);
    }

    private
    function fetchData(array $data, Model $model): array
    {
        $modelData = [];
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $modelData[$value] = $model->{$value};
            } else {
                $relation = $model->{$key};

                if ($relation instanceof Model) {
                    $modelData[$key] = $this->fetchData($value, $relation);
                } elseif ($relation instanceof Collection) {
                    $modelData[$key] = [];
                    foreach ($relation as $item) {
                        $modelData[$key][] = $this->fetchData($value, $item);
                    }
                }
            }
        }

        return $modelData;
    }
}
