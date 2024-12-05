<?php

namespace App\ESModule\Syncer\Creator\Sync;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Creator\Sync\Exception\GrouperException;
use App\ESModule\Syncer\Eloquent\DatabaseToIndexSyncMap\DatabaseToIndexSyncMapCreator;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class SyncRowsCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly DatabaseToIndexSyncMapCreator $databaseToIndexSyncMapCreator,
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
        $syncDtosGrouped = [];
        foreach ($cdcDtos as $cdcDto) {
            $databaseName = $cdcDto->getDatabaseName();
            $tableName = $cdcDto->getTableName();
            $type = $cdcDto->getType();
            $data = $cdcDto->getData();
            $changedFields = $cdcDto->getChangedFields();

            if (!$this->databaseToIndexSyncMapCreator->isSyncDatabaseNameAndTableName($databaseName, $tableName)) {
                // TODO check $databaseToIndexSyncMap
                continue;
            }

            // detect identifier
            $identifierValue = $this->modelMapper->detectIdentifierValue($tableName, $data);

            $type = $cdcDto->getType();
            if (CdcDto::TYPE_DELETE === $type) {
                if (isset($syncDtosGrouped[$tableName][$identifierValue]) && CdcDto::TYPE_DELETE === $syncDtosGrouped[$tableName][$identifierValue]->getType()) {
                    throw new GrouperException('Grouper: delete already deleted');
                }

                $syncDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_DELETE, $identifierValue);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($syncDtosGrouped[$tableName][$identifierValue])) {
                    $syncDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_UPSERT, $identifierValue);
                } else {
                    /** @var SyncDto $syncDto */
                    $syncDto = $syncDtosGrouped[$tableName][$identifierValue];

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

                        $syncDtosGrouped[$tableName][$identifierValue] = $syncDto;
                    }
                }
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($syncDtosGrouped[$tableName][$identifierValue])) {
                    throw new GrouperException('Grouper: insert detected after insert, delete or update');
                }

                $syncDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_UPSERT, $identifierValue);
            }
        }

        $syncDtos = [];
        foreach ($syncDtosGrouped as $tableName => $data) {
            foreach ($data as $identifierValue => $syncDto) {
                $syncDtos[] = $syncDto;
            }
        }

        return $syncDtos;
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
