<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Illuminate\Database\Eloquent\Model;

class ModelRootFetcher
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly SyncMapper $syncMapper,
    ) {
    }

    public function fetch(SyncItemDto $syncItemDto): ?Model
    {
        $tableName = $syncItemDto->getTableName();

        if (!$this->syncMapper->isTableNameForIndex($tableName)) {
            return null;
        }

        $className = $this->modelMapper->convertTableNameToClassName($tableName);
        $modelRoot = $this->modelMapper->fetchModel($className, $syncItemDto);

        return $modelRoot;
    }
}
