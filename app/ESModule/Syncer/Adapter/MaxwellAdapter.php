<?php

namespace App\ESModule\Syncer\Adapter;

use App\ESModule\Syncer\Dto\ChangedDbRow;

class MaxwellAdapter
{
    public const string INSERT = 'insert';
    public const string UPDATE = 'update';
    public const string DELETE = 'delete';

    public function convert(string $data): ChangedDbRow
    {
        $data = json_decode($data, true);

        $database = $data['database'];
        $table = $data['table'];
        $type = $data['type'];

        // TODO
        $identifier = $data['data']['id'];

        $changedFields = [];
        if (self::UPDATE === $type) {
            foreach ($data['old'] as $key => $value) {
                $changedFields[] = $key;
            }
        }

        return new ChangedDbRow($database, $table, $identifier, $type, $changedFields);
    }
}
