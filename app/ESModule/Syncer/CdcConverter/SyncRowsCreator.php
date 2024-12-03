<?php

namespace App\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\CdcConverter\Exception\GrouperException;
use App\ESModule\Syncer\Eloquent\ModelMapper;

class SyncRowsCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
    ) {
    }

    /**
     * @param array<CdcDto> $cdcDtos
     *
     * @return array<SyncRowDto>
     *
     * @throws GrouperException
     */
    public function create(array $cdcDtos): array
    {
        $syncRowDtosGrouped = [];
        foreach ($cdcDtos as $cdcDto) {
            /** @var CdcDto $cdcDto */
            $database = $cdcDto->getDatabase();
            $table = $cdcDto->getTable();
            $type = $cdcDto->getType();
            $data = $cdcDto->getData();
            $changedFields = $cdcDto->getChangedFields();

            if (!$this->modelMapper->syncForDatabaseAndTableName($database, $table)) {
                // TODO we should check if that table is in ES
                continue;
            }

            // detect identifier
            $identifier = $this->detectIdentifier($cdcDto);

            $type = $cdcDto->getType();
            if (CdcDto::TYPE_DELETE === $type) {
                if (isset($syncRowDtosGrouped[$table][$identifier]) && CdcDto::TYPE_DELETE === $syncRowDtosGrouped[$table][$identifier]->getType()) {
                    throw new GrouperException('Grouper: delete already deleted');
                }

                $syncRowDtosGrouped[$table][$identifier] = $this->createSyncDbRow($cdcDto, SyncRowDto::TYPE_DELETE, $identifier);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($syncRowDtosGrouped[$table][$identifier])) {
                    $syncRowDtosGrouped[$table][$identifier] = $this->createSyncDbRow($cdcDto, SyncRowDto::TYPE_UPSERT, $identifier);
                } else {
                    /** @var CdcDto $syncRowDto */
                    $syncRowDto = $syncRowDtosGrouped[$table][$identifier];

                    if (SyncRowDto::TYPE_DELETE === $syncRowDto->getType()) {
                        throw new GrouperException('Grouper: update detected after delete');
                    }

                    if (SyncRowDto::TYPE_UPSERT === $syncRowDto->getType()) {
                        $changedFields = array_unique(array_merge(
                            $syncRowDto->getChangedFields(),
                            $changedFields
                        ));

                        $syncRowDto->setChangedFields($changedFields);
                        $syncRowDto->setData($cdcDto->getData());

                        $syncRowDtosGrouped[$table][$identifier] = $syncRowDto;
                    }
                }
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($syncRowDtosGrouped[$table][$identifier])) {
                    throw new GrouperException('Grouper: insert detected after insert, delete or update');
                }

                $syncRowDtosGrouped[$table][$identifier] = $this->createSyncDbRow($cdcDto, SyncRowDto::TYPE_UPSERT, $identifier);
            }
        }

        $syncRowDtos = [];
        foreach ($syncRowDtosGrouped as $table => $data) {
            foreach ($data as $identifier => $syncRowDto) {
                $syncRowDtos[] = $syncRowDto;
            }
        }

        return $syncRowDtos;
    }

    private function createSyncDbRow(CdcDto $cdcDto, string $type, mixed $identifier): SyncRowDto
    {
        return new SyncRowDto(
            $cdcDto->getDatabase(),
            $cdcDto->getTable(),
            $type,
            $cdcDto->getData(),
            $cdcDto->getChangedFields(),
            $identifier,
        );
    }

    public function detectIdentifier(CdcDto $cdcDto): mixed
    {
        // TODO
        return $cdcDto->getData()['id'];
    }
}
