<?php

namespace App\Support\Interfaces;

/**
 * @property int $id
 */
interface EventNotifiableContract
{
    public function getObjectName(): string;
}
