<?php

namespace App\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Dto\ChangedDbRow;

class MaxwellConverter
{
    public const string INSERT = 'insert';
    public const string UPDATE = 'update';
    public const string DELETE = 'delete';

    public function convert(string $payload): ChangedDbRow
    {
        $payload = json_decode($payload, true);

        $database = $payload['database'];
        $table = $payload['table'];
        $type = $payload['type'];
        $identifier = $payload['data']['id'];
        $changedFields = isset($payload['old']) ? array_keys($payload['old']) : [];
        $data = $payload['data'];

        // TODO

        $changedFields = [];
        if (self::UPDATE === $type) {
            foreach ($payload['old'] as $key => $value) {
                $changedFields[] = $key;
            }
        }

        return new ChangedDbRow($database, $table, $type, $identifier, $changedFields, $data);
    }
}
