<?php

namespace App\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Dto\ChangedDbRow;
use App\ESModule\Cdc\Dto\SyncDbRow;
use App\ESModule\Cdc\Exception\GrouperException;

class Grouper
{
    public function group($data2): array
    {
        $data = [];
        foreach ($data2 as $changedDbRow) {
            $table = $changedDbRow->getTable();
            $identifier = $changedDbRow->getIdentifier();
            $changedFields = $changedDbRow->getChangedFields();

            $type = $changedDbRow->getType();
            if (ChangedDbRow::TYPE_DELETE === $type) {
                if (isset($data[$table][$identifier]) &&  $data[$table][$identifier]->getType() === ChangedDbRow::TYPE_DELETE){
                    throw new GrouperException('Grouper: delete already deleted');
                }

                $data[$table][$identifier] = $this->createSyncDbRow($changedDbRow, ChangedDbRow::TYPE_DELETE);
            } elseif (ChangedDbRow::TYPE_UPDATE === $type) {
                if (!isset($data[$table][$identifier])){
                    $data[$table][$identifier] = $this->createSyncDbRow($changedDbRow, SyncDbRow::TYPE_UPSERT);
                }else{
                    /** @var SyncDbRow $syncDbRowExisting */
                    $syncDbRowExisting = $data[$table][$identifier];

                    if($syncDbRowExisting->getType() === ChangedDbRow::TYPE_DELETE){
                        throw new GrouperException('Grouper: update detected after delete');
                    }

                    if($syncDbRowExisting->getType() === SyncDbRow::TYPE_UPSERT){
                        $changedFields = array_unique(array_merge(
                            $syncDbRowExisting->getChangedFields(),
                            $changedFields
                        ));

                        $syncDbRowExisting->setChangedFields($changedFields);
                        $syncDbRowExisting->setData($changedDbRow->getData());

                        $data[$table][$identifier] = $syncDbRowExisting;
                    }
                }
            } elseif (ChangedDbRow::TYPE_INSERT === $type) {
                if (isset($data[$table][$identifier])){
                    throw new GrouperException('Grouper: insert detected after delete or update');
                }

                $data[$table][$identifier] = $this->createSyncDbRow($changedDbRow, SyncDbRow::TYPE_UPSERT);
            }
        }

        return $data;
    }

    private function createSyncDbRow(ChangedDbRow $changedDbRow, string $type): SyncDbRow
    {
        return new SyncDbRow(
            $changedDbRow->getDatabase(),
            $changedDbRow->getTable(),
            $type,
            $changedDbRow->getIdentifier(),
            $changedDbRow->getChangedFields(),
            $changedDbRow->getData()
        );
    }
}
