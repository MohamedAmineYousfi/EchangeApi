<?php

namespace App\Support\Pipes;

use Closure;
use Spatie\Activitylog\Contracts\LoggablePipe;
use Spatie\Activitylog\EventLogBag;

class RolePermissionsPipe implements LoggablePipe
{
    public function __construct()
    {
    }

    public function handle(EventLogBag $event, Closure $next): EventLogBag
    {

        if ($event->changes && isset($event->changes['old'])) {
            $event->changes['old']['permissions'] = json_decode($event->changes['old']['permissions_list']);
            unset($event->changes['old']['permissions_list']);
        }
        if (isset($event->changes['attributes'])) {
            $event->changes['attributes']['permissions'] = json_decode($event->changes['attributes']['permissions_list']);
            unset($event->changes['attributes']['permissions_list']);
        }

        return $next($event);
    }
}
