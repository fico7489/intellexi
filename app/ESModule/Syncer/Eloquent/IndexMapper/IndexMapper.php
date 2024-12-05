<?php

namespace App\ESModule\Syncer\Eloquent\IndexMapper;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Eloquent\DatabaseToIndexSyncMap\DatabaseToIndexSyncMapCreator;
use App\ESModule\Syncer\Eloquent\ModelMapper;

class IndexMapper
{
    public function __construct(
        private readonly DatabaseToIndexSyncMapCreator $databaseToIndexSyncMapCreator,
        private readonly ModelMapper $modelMapper
    )
    {
    }

    public function fetchClassNamesIndex(): array
    {
        $databaseToIndexSyncMap = $this->databaseToIndexSyncMapCreator->create();

        $classNamesIndex = [];
        foreach ($databaseToIndexSyncMap as $tableName => $tableData) {
            foreach ($tableData as $sync) {
                dump(111);
                /** @var IndexDefinerModelInterface $index */
                $index = $sync['index'];

                $className = $index->getClassName();
                $classNamesIndex[$className] = $index;
            }
        }

        return $classNamesIndex;
    }

    /*public function fetchTableNamesIndex(): array
    {
        $databaseToIndexSyncMap = $this->databaseToIndexSyncMapCreator->create();

        $classNamesIndex = [];
        foreach ($databaseToIndexSyncMap as $tableName => $tableData) {
            foreach ($tableData as $sync) {
                $index = $sync['index'];

                $className = $index->getClassName();
                $tableName = $tableData-$this->modelMapper->convertClassNameToTableName($className);
                $classNamesIndex[$index->getClassName()] = $index;
            }
        }

        return $classNamesIndex;
    }*/
}
