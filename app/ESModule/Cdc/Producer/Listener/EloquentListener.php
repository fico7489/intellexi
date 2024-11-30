<?php

namespace App\ESModule\Cdc\Producer\Listener;

use App\ESModule\Cdc\Producer\Dispatcher\DispatcherInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

readonly class EloquentListener implements ListenerInterface
{
    public function __construct(
        private DispatcherInterface $dispatcher,
    ) {
    }

    public function listen(): void
    {
        Event::listen(['eloquent.created: *'], function ($event, $models) {
            foreach ($models as $model) {
                $data = $this->createArray($model, 'insert');

                $this->dispatch($data);
            }
        });

        Event::listen(['eloquent.updated: *'], function ($event, $models) {
            foreach ($models as $model) {
                $data = $this->createArray($model, 'update');

                $this->dispatch($data);
            }
        });

        Event::listen(['eloquent.deleted: *'], function ($event, $models) {
            foreach ($models as $model) {
                $data = $this->createArray($model, 'delete');

                $this->dispatch($data);
            }
        });
    }

    private function dispatch(array $data): void
    {
        dump('dispatched', $data);

        $this->dispatcher->dispatch($data);
    }

    private function createArray(Model $model, string $type): array
    {
        $changedFields = [];
        if ('update' === $type) {
            $changedFields = $this->detectChangedFields($model);
        }

        return [
            'database' => $model->getConnection()->getDatabaseName(),
            'table' => $model->getTable(),
            'identifier' => $model->getKey(),
            'type' => $type,
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
