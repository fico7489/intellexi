<?php

namespace App\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Dto\ChangedDbRow;

readonly class Grouper
{
    public function __construct(
        private ConverterInterface $converter,
    )
    {
    }

    public function group($payloads): array
    {
        $data = [];
        foreach ($payloads as $payload) {
            $payload = json_decode($payload, true);

            $changedDbRow = $this->converter->convert($payload);

            $table = $changedDbRow->getTable();
            $identifier = $changedDbRow->getIdentifier();
            $changedFields = $changedDbRow->getChangedFields();

            $type = $changedDbRow->getType();
            if (ChangedDbRow::TYPE_DELETE === $type) {
                $data[$table][$identifier] = [
                    'type' => 'delete',
                ];
            } elseif (ChangedDbRow::TYPE_UPDATE === $type) {
                if (isset($data[$table][$changedDbRow->getIdentifier()])) {
                    $data[$table][$identifier]['changed_fields'] = array_unique(array_merge(
                        $data[$table][$identifier]['changed_fields'],
                        $changedFields
                    ));
                } else {
                    $data[$table][$identifier] = [
                        'type' => 'update',
                        'changed_fields' => $changedFields,
                    ];
                }
            } elseif (ChangedDbRow::TYPE_INSERT === $type) {
                $data[$table][$identifier] = [
                    'type' => 'insert',
                ];
            }
        }

        return $data;
    }
}
