<?php

namespace App\ESModule\Cdc\Producer\Eloquent;

use App\ESModule\Cdc\Producer\Dispatcher\DispatcherInterface;
use App\ESModule\Cdc\Producer\Dispatcher\RedisPublish;
use App\ESModule\Syncer\Adapter\MaxwellAdapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class EloquentListener
{
    public function listen(): void
    {
        Event::listen(['eloquent.updated: *'], function ($event, $models) {
            foreach ($models as $model) {
                $changedFields = $this->detectChangedFields($model);

                $data = $this->createArray($model, $changedFields);

                $this->dispatch($data);
            }
        });

        Event::listen(['eloquent.created: *'], function ($event, $models) {
            foreach ($models as $model) {
                $data = $this->createArray($model);

                $this->dispatch($data);
            }
        });

        Event::listen(['eloquent.deleted: *'], function ($event, $models) {
            foreach ($models as $model) {
                $data = $this->createArray($model);

                $this->dispatch($data);
            }
        });
    }

    private function dispatch(array $data): void
    {
        dump('dispatched', $data);

        // TODO @DispatcherInterface
        app(RedisPublish::class)->dispatch($data);
    }

    private function createArray(Model $model, array $changedFields = []): array
    {
        return [
            'database' => $model->getConnection()->getDatabaseName(),
            'table' => $model->getTable(),
            'identifier' => $model->getKey(),
            'type' => MaxwellAdapter::UPDATE,
            'changedFields' => $changedFields,
        ];
    }

    private function detectChangedFields(Model $model): array
    {
        /** @var Model $model */
        $changedFields = [];
        foreach ($model->getChanges() as $key => $value) {
            $changedFields[] = $key;
        }

        return $changedFields;
    }
}
