<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @property string $event
 * @property string $notification_type
 * @property string $object_type
 * @property string $object_id
 */
class Notification extends DatabaseNotification
{
    protected static function booted()
    {
        static::addGlobalScope('notification-user', function (Builder $builder) {
            /** @var ?User */
            $user = auth()->user();
            if ($user) {
                $builder->where('notifiable_type', '=', $user->getMorphClass());
                $builder->where('notifiable_id', '=', $user->id);
            }
        });
    }
}
