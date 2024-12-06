<?php

namespace App\ESModule\Config\Interface;

interface IndexSyncInterface
{
    public function syncMap($syncMap): array;

    public function syncModels($syncModels): array;
}
