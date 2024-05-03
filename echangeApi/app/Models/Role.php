<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Interfaces\ResellerScopable;
use App\Support\Pipes\RolePermissionsPipe;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use App\Support\Traits\ResellerScoped;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\PermissionRegistrar;

/**
 * {@inheritDoc}
 *
 * @property Collection<Permission> $permissions
 */
class Role extends \Spatie\Permission\Models\Role implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable, ResellerScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use ResellerScoped {
        \App\Support\Traits\ResellerScoped::booted as resellerScopedBooted;
    }
    use SoftDeletes;

    public const SUPER_ADMIN_ROLE_NAME = 'Super Admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'excerpt',
        'guard_name',
        'permissions_list',
        'organization_id',
        'reseller_id',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::addLogChange(new RolePermissionsPipe());
        self::resellerScopedBooted();
        self::organizationScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();

        static::saving(function ($role) {
            if ($role->organization) {
                $role->reseller()->associate($role->organization->reseller);
            }

            try {
                $requestData = request()['data'];
                if (
                    isset($requestData['relationships']) &&
                    isset($requestData['relationships']['permissions']) &&
                    isset($requestData['relationships']['permissions']['data']) &&
                    $requestData &&
                    $requestData['relationships'] &&
                    $requestData['relationships']['permissions'] &&
                    $requestData['relationships']['permissions']['data'] &&
                    is_array($requestData['relationships']['permissions']['data'])
                ) {
                    $permissions = [];
                    $requestPermissions = $requestData['relationships']['permissions']['data'];
                    foreach ($requestPermissions as $perm) {
                        if ($perm['type'] == 'permissions') {
                            $permissions[] = $perm['id'];
                        }
                    }
                    $permissions = Permission::whereIn('id', $permissions)->select('id', 'key', 'name')->get();
                    $formattedPermissions = [];
                    foreach ($permissions as $perm) {
                        $formattedPermissions[$perm['key']] = $perm;
                    }
                    $role->permissions_list = json_encode($formattedPermissions);
                }
            } catch (Exception $e) {
                throw $e;
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['packages', 'users'];
    }

    public function getObjectName(): string
    {
        return $this->name;
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            PermissionRegistrar::$pivotRole,
            PermissionRegistrar::$pivotPermission
        )->whereRaw('
            permissions.key IN (
                SELECT p.key 
                FROM permissions p
                    INNER JOIN role_has_permissions rhp ON p.id = rhp.permission_id
                    INNER JOIN roles r ON r.id = rhp.role_id
                WHERE rhp.role_id = role_has_permissions.role_id
                    AND (
                        CASE
                            WHEN r.organization_id IS NULL THEN true
                            ELSE p.key IN (
                                SELECT p2.key 
                                FROM subscriptions sub
                                    JOIN packages pkg ON sub.package_id = pkg.id
                                    JOIN role_has_permissions rhp2 ON pkg.default_role_id = rhp2.role_id
                                    JOIN permissions p2 ON rhp2.permission_id = p2.id
                                WHERE sub.organization_id = r.organization_id
                                    AND sub.end_time >= NOW()
                            )
                        END
                    )
                GROUP BY p.key
            )
        ');
    }

    /**
     * Undocumented function
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'default_role_id');
    }

    public function scopeName($query, $name): Builder
    {
        return $query->where('name', 'LIKE', "%$name%", 'or');
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('roles.id', $ids);
    }
}
