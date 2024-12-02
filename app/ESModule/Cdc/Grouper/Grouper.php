<?php

namespace App\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Dto\ChangedRowDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Exception\GrouperException;

class Grouper
{
    public function group($data2): array
    {
        $data = [];
        foreach ($data2 as $changedDbRow) {
            $table = $changedDbRow->getTable();
            $idddentifier = 1;  //TODO
            $changedFields = $changedDbRow->getChangedFields();

            $type = $changedDbRow->getType();
            if (ChangedRowDto::TYPE_DELETE === $type) {
                if (isset($data[$table][$idddentifier]) && ChangedRowDto::TYPE_DELETE === $data[$table][$idddentifier]->getType()) {
                    throw new GrouperException('Grouper: delete already deleted');
                }

                $data[$table][$idddentifier] = $this->createSyncDbRow($changedDbRow, ChangedRowDto::TYPE_DELETE);
            } elseif (ChangedRowDto::TYPE_UPDATE === $type) {
                if (!isset($data[$table][$idddentifier])) {
                    $data[$table][$idddentifier] = $this->createSyncDbRow($changedDbRow, ChangedRowGroupedDto::TYPE_UPSERT);
                } else {
                    /** @var ChangedRowGroupedDto $syncDbRowExisting */
                    $syncDbRowExisting = $data[$table][$idddentifier];

                    if (ChangedRowDto::TYPE_DELETE === $syncDbRowExisting->getType()) {
                        throw new GrouperException('Grouper: update detected after delete');
                    }

                    if (ChangedRowGroupedDto::TYPE_UPSERT === $syncDbRowExisting->getType()) {
                        $changedFields = array_unique(array_merge(
                            $syncDbRowExisting->getChangedFields(),
                            $changedFields
                        ));

                        $syncDbRowExisting->setChangedFields($changedFields);
                        $syncDbRowExisting->setData($changedDbRow->getData());

                        $data[$table][$idddentifier] = $syncDbRowExisting;
                    }
                }
            } elseif (ChangedRowDto::TYPE_INSERT === $type) {
                if (isset($data[$table][$idddentifier])) {
                    throw new GrouperException('Grouper: insert detected after insert, delete or update');
                }

                $data[$table][$idddentifier] = $this->createSyncDbRow($changedDbRow, ChangedRowGroupedDto::TYPE_UPSERT);
            }
        }

        return $data;
    }

    private function createSyncDbRow(ChangedRowDto $changedDbRow, string $type): ChangedRowGroupedDto
    {
        return new ChangedRowGroupedDto(
            $changedDbRow->getDatabase(),
            $changedDbRow->getTable(),
            $type,
            $changedDbRow->getChangedFields(),
            $changedDbRow->getData()
        );
    }
}
