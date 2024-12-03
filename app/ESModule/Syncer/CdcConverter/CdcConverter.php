<?php

namespace App\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\CdcConverter\Exception\GrouperException;
use App\ESModule\Syncer\Eloquent\ModelMapper;

class CdcConverter
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
    ) {
    }

    /**
     * @param array $cdcDtos <CdcDto>
     *
     * @throws GrouperException
     */
    public function convert(array $cdcDtos): array
    {
        $syncRowDtos = [];
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
                if (isset($syncRowDtos[$table][$identifier]) && CdcDto::TYPE_DELETE === $syncRowDtos[$table][$identifier]->getType()) {
                    throw new GrouperException('Grouper: delete already deleted');
                }

                $syncRowDtos[$table][$identifier] = $this->createSyncDbRow($cdcDto, SyncRowDto::TYPE_DELETE, $identifier);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($syncRowDtos[$table][$identifier])) {
                    $syncRowDtos[$table][$identifier] = $this->createSyncDbRow($cdcDto, SyncRowDto::TYPE_UPSERT, $identifier);
                } else {
                    /** @var CdcDto $syncRowDto */
                    $syncRowDto = $syncRowDtos[$table][$identifier];

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

                        $syncRowDtos[$table][$identifier] = $syncRowDto;
                    }
                }
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($syncRowDtos[$table][$identifier])) {
                    throw new GrouperException('Grouper: insert detected after insert, delete or update');
                }

                $syncRowDtos[$table][$identifier] = $this->createSyncDbRow($cdcDto, SyncRowDto::TYPE_UPSERT, $identifier);
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
