<?php

namespace App\ESModule\Cdc\Grouper;

class Grouper
{
    public function group($payloads): array
    {
        $data = [];
        foreach ($payloads as $payload) {
            $payload = json_decode($payload, true);

            $table = $payload['table'];
            $type = $payload['type'];
            $identifier = $payload['data']['id'];

            if ($type === 'delete') {
                $data[$table][$identifier] = [
                    'type' => 'delete',
                ];
            } elseif ($type === 'update') {
                if (isset($data[$table][$identifier])) {
                    $data[$table][$identifier]['changed_fields'] = array_unique(array_merge(
                        $data[$table][$identifier]['changed_fields'],
                        array_keys($payload['old'])
                    ));
                } else {
                    $data[$table][$identifier] = [
                        'type' => 'update',
                        'changed_fields' => array_keys($payload['old']),
                    ];
                }
            }
        }

        dump($data);

        return $data;
    }
}
