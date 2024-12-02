<?php

namespace App\ESModule\Cdc\Dto;

class SyncDbRow extends ChangedDbRow
{
    final public const string TYPE_UPSERT = 'upsert';
}
