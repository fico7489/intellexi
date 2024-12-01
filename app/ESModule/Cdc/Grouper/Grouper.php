<?php

namespace App\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Converter\MaxwellConverter;

class Grouper
{
    public function group($payloads): array
    {
        $data = [];
        foreach ($payloads as $payload) {
            $payload = json_decode($payload, true);

            /** @var MaxwellConverter $converter */
            $converter = app(MaxwellConverter::class);

            list($table, $type, $identifier, $old) = $converter->convert($payload);

            if ('delete' === $type) {
                $data[$table][$identifier] = [
                    'type' => 'delete',
                ];
            } elseif ('update' === $type) {
                if (isset($data[$table][$identifier])) {
                    $data[$table][$identifier]['changed_fields'] = array_unique(array_merge(
                        $data[$table][$identifier]['changed_fields'],
                        $old
                    ));
                } else {
                    $data[$table][$identifier] = [
                        'type' => 'update',
                        'changed_fields' => $old,
                    ];
                }
            } elseif ('insert' === $type) {
                $data[$table][$identifier] = [
                    'type' => 'insert',
                ];
            }
        }

        return $data;
    }
}
