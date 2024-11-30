<?php

namespace App\ESModule\Cdc\Producer\Listener;

use App\ESModule\Syncer\Adapter\MaxwellAdapter;

/**
 * Listen to database changes and dispatch changes to DispatcherInterface with this format
 *
 * [
 *   'database' => string,
 *   'table' => string,
 *   'identifier' => mixed,
 *   'type' => string[insert|update|delete],
 *   'changedFields' => array,
 * ]
 */
interface ListenerInterface
{
    public function listen(): void;
}
