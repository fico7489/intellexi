<?php

namespace App\ESModule\Cdc\Converter;

class MaxwellConverter{
    public function convert(array $payload){
        $table = $payload['table'];
        $type = $payload['type'];
        $identifier = $payload['data']['id'];
        $old = isset($payload['old']) ?array_keys($payload['old']) : [];

        return [
            $table,
            $type,
            $identifier,
            $old,
        ];
    }
}
