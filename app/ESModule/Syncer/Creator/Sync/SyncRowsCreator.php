<?php

namespace App\ESModule\Syncer\Creator\Sync;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Creator\Sync\Exception\GrouperException;
use App\ESModule\Syncer\Mapper\DatabaseMapper\DatabaseMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;

class SyncRowsCreator
{
    public function __construct(
        private readonly DatabaseMapper $databaseMapper,
        private readonly SyncMapper $databaseToIndexSyncMapCreator,
    ) {
    }

    /**
     * It converts CdcDtos to SyncDtos.
     *
     * @param array<CdcDto> $cdcDtos
     *
     * @return array<SyncDto>
     *
     * @throws GrouperException
     */
    public function create(array $cdcDtos): array
    {
        $syncDtos = [];
        foreach ($cdcDtos as $cdcDto) {
            $databaseName = $cdcDto->getDatabaseName();
            $tableName = $cdcDto->getTableName();
            $type = $cdcDto->getType();
            $data = $cdcDto->getData();
            $changedFields = $cdcDto->getChangedFields();

            if (!$this->databaseToIndexSyncMapCreator->isTableNameForSync($tableName)) {
                // TODO check $databaseToIndexSyncMap
                continue;
            }

            // detect identifier
            $identifierValue = $this->databaseMapper->detectIdentifierValue($tableName, $data);

            $type = $cdcDto->getType();
            if (CdcDto::TYPE_DELETE === $type) {
                if (isset($syncDtos[$tableName][$identifierValue]) && CdcDto::TYPE_DELETE === $syncDtos[$tableName][$identifierValue]->getType()) {
                    throw new GrouperException('Grouper: delete already deleted');
                }

                $syncDtos[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_DELETE, $identifierValue);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($syncDtos[$tableName][$identifierValue])) {
                    $syncDtos[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_UPSERT, $identifierValue);
                } else {
                    /** @var SyncDto $syncDto */
                    $syncDto = $syncDtos[$tableName][$identifierValue];

                    if (SyncDto::TYPE_DELETE === $syncDto->getType()) {
                        throw new GrouperException('Grouper: update detected after delete');
                    }

                    if (SyncDto::TYPE_UPSERT === $syncDto->getType()) {
                        $changedFields = array_unique(array_merge(
                            $syncDto->getChangedFields(),
                            $changedFields
                        ));

                        $syncDto->setChangedFields($changedFields);
                        $syncDto->setData($cdcDto->getData());

                        $syncDtos[$tableName][$identifierValue] = $syncDto;
                    }
                }
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($syncDtos[$tableName][$identifierValue])) {
                    throw new GrouperException('Grouper: insert detected after insert, delete or update');
                }

                $syncDtos[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_UPSERT, $identifierValue);
            }
        }

        $syncDtos2 = [];
        foreach ($syncDtos as $tableName => $data) {
            foreach ($data as $identifierValue => $syncDto) {
                $syncDtos2[] = $syncDto;
            }
        }

        return $syncDtos2;
    }

    private function createSyncDbRow(CdcDto $cdcDto, string $type, mixed $identifierValue): SyncDto
    {
        return new SyncDto(
            $cdcDto->getTableName(),
            $type,
            $cdcDto->getData(),
            $cdcDto->getChangedFields(),
            $identifierValue,
        );
    }
}
