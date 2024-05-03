<?php

namespace App\Models;

use App\Constants\Permissions;
use App\Models\Scopes\PermissionsScope;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends \Spatie\Permission\Models\Permission
{
    use LogsActivity;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new PermissionsScope());
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * getPermissionScope
     */
    public static function getPermissionScope(string $permission): int
    {
        return Permissions::PERMISSIONS__SCOPE[$permission];
    }

    public function scopeName($query, $name): Builder
    {
        return $query->where('name', 'LIKE', "%$name%", 'or');
    }
}
