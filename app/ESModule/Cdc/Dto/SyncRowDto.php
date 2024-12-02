<?php

namespace App\ESModule\Cdc\Dto;

class SyncRowDto extends ChangedRowDto
{
    final public const string TYPE_UPSERT = 'upsert';
}
