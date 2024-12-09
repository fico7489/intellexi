<?php

namespace App\ESModule\Syncer\Creator\SyncableDocument\Helper;

class ShouldSyncDetector
{
    public function detect(array $changedFieldsTriggered, array $changedFieldsTriggers): bool
    {
        return !empty(array_intersect($changedFieldsTriggered, $changedFieldsTriggers));
    }
}
