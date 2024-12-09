<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Cdc\Dto\CdcDto;

class ShouldSyncDetector
{
    public function detect(string $type, array $changedFieldsTriggered, array $changedFieldsTriggers): bool
    {
        if (in_array($type, [CdcDto::TYPE_INSERT, CdcDto::TYPE_DELETE])) {
            return true;
        }

        return !empty(array_intersect($changedFieldsTriggered, $changedFieldsTriggers));
    }
}
