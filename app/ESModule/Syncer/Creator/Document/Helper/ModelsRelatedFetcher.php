<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class ModelsRelatedFetcher
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
    ) {
    }

    public function fetch(SyncItemDto $syncItemDto, IndexDefinerModelInterface $index, $modelRoot)
    {
        $tableName = $syncItemDto->getTableName();
        $syncModels = $index->syncModels([]);

        if ($this->modelMapper->isTableNameModel($tableName)) {
            $className = $this->modelMapper->convertTableNameToClassName($tableName);

            if ($index->getClassName() === $className) {
                // root model
                return [$modelRoot];
            }

            if (isset($syncModels[$className])) {
                return $syncModels[$className]($modelRoot, $syncItemDto, []);
            }
        }

        if (isset($syncModels[$tableName])) {
            return $syncModels[$tableName]($modelRoot, $syncItemDto, []);
        }

        return [];
    }
}
