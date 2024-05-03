<?php

namespace App\Models;

use App\Constants\BillingInformations;
use App\Constants\Permissions;
use App\Exceptions\ConstraintException;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\ModelIsBillableTo;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Interfaces\ResellerScopable;
use App\Support\Interfaces\Scopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\ModelHasBillingInformations;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use App\Support\Traits\ResellerScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

/**
 * {@inheritDoc}
 *
 * @property array $other_phones
 * @property Collection<Role> $roles
 */
class User extends Authenticatable implements EventNotifiableContract, ModelIsBillableTo, OnDeleteRelationsCheckable, OrganizationScopable, ResellerScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasApiTokens;
    use HasPushSubscriptions;
    use HasRoles {
        checkPermissionTo as defaultCheckPermissionTo;
    }
    use LogsActivity;
    use ModelHasBillingInformations {
        \App\Support\Traits\ModelHasBillingInformations::booted as modelHasBillingInformationsBooted;
    }
    use Notifiable;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'firstname',
        'lastname',
        'email',
        'phone',
        'phone_extension',
        'phone_type',
        'other_phones',
        'profile_image',
        'locale',
        'is_staff',
        'password',
        'roles',
        'organization',
        'reseller',
        'restrict_to_locations',
        'two_fa_code',
        'verification_code_expires_at',
        'is_2fa_enabled',
        'two_fa_disabled_at',
        'two_fa_enabled_at',
        ...BillingInformations::MODEL_BILLING_INFORMATIONS_FILLABLES,
    ];

    protected $guardName = 'api';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::resellerScopedBooted();
        self::organizationScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();
        self::modelHasBillingInformationsBooted();

        static::creating(function (User $model) {
            $model->active = true;
        });

        static::saving(function (User $model) {
            if ($model->organization) {
                $model->reseller()->associate($model->organization->reseller);
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['ownedOrganizations', 'ownedResellers'];
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function getObjectName(): string
    {
        return $this->firstname.' '.$this->lastname;
    }

    /**
     * Get the other phones
     */
    protected function otherPhones(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Undocumented function
     */
    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_id');
    }

    /**
     * Undocumented function
     */
    public function ownedResellers(): HasMany
    {
        return $this->hasMany(Reseller::class, 'owner_id');
    }

    /**
     * Mutator for hashing the password on save
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function scopeName($query, $name): Builder
    {
        return $query->where('users.name', 'LIKE', "%$name%", 'or');
    }

    public function scopeEmail($query, $email): Builder
    {
        return $query->where('users.email', 'LIKE', "%$email%", 'or');
    }

    public function scopeRoles($query, $role): Builder
    {
        return $query->orWhereHas('roles', function ($query) use ($role) {
            $query->where('roles.id', '=', "$role");
        });
    }

    /**
     * @param  $name
     */
    public function scopeIsStaff($query, $isStaff): Builder
    {
        return $query->where('users.is_staff', '=', filter_var($isStaff, FILTER_VALIDATE_BOOLEAN), 'and');
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where(DB::raw('CONCAT(users.firstname, " ", users.lastname)'), 'LIKE', "%$search%", 'or')
            ->where('users.phone', 'LIKE', "%$search%", 'or')
            ->where('users.email', 'LIKE', "%$search%", 'or');
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('users.id', $ids);
    }

    public function scopeNotLinkedToLocation(Builder $query, int $locationId): Builder
    {
        return $query->whereDoesntHave('allowedLocations', function (Builder $query) use ($locationId) {
            $query->where('id', $locationId);
        });
    }

    /**
     * @return bool|null
     *
     * @throws ConstraintException
     */
    public function delete()
    {
        if ($this->id == auth()->id()) {
            throw new ConstraintException('You cannot delete yourself.');
        }

        return parent::delete();
    }

    /**
     * we have to make a raw request to get the allowed locations for this user. if not the scope will
     * make an infinite loop : select user allowed locations -> scope -> select user allowed locations
     *
     * @return array<int>
     */
    public function getAllowedLocationsRaw(): array
    {
        $allowedLocations = DB::select('
            SELECT location_id FROM model_allowed_locations 
            JOIN locations ON location_id = locations.id
            WHERE model_id = :user_id
            AND model_type = :user_class
            AND locations.deleted_at IS NULL
        ', [
            'user_id' => $this->id,
            'user_class' => User::class,
        ]);
        $allowedLocations = array_map(function ($item) {
            return $item->location_id;
        }, $allowedLocations);

        return $allowedLocations;
    }

    /**
     * can a user access an organization scopable model
     */
    public function canAccessModelWithPermission(Scopable $scopable, string $permission): bool
    {
        if ($this->is_staff) {
            return $this->can($permission);
        }

        if ($scopable instanceof OrganizationScopable) {
            if ($this->organization && $scopable->getOrganization()) {
                /** @var Collection */
                $organizationPermissions = $this->organization->getActivePermissions()->pluck('name');
                if ($organizationPermissions->contains($permission)) {
                    if ($this->organization->id === $scopable->getOrganization()->id) {
                        return $this->can($permission);
                    }
                }
            }
            if ($this->reseller && $scopable->getOrganization()) {
                if ($this->reseller->id === $scopable->getOrganization()->reseller->id) {
                    return $this->can($permission);
                }
            }
        }

        if ($scopable instanceof ResellerScopable) {
            if ($this->reseller) {
                if ($this->reseller->id === $scopable->getReseller()->id) {
                    return $this->can($permission);
                }
            }
        }

        return false;
    }

    /**
     * Undocumented function
     */
    public function checkPermissionTo(string|int|Permission $permission, $guardName = null): bool
    {
        /** @var Permission */
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            try {
                $permission = $permissionClass->findByName(
                    $permission,
                    $guardName ?? $this->getDefaultGuardName()
                );
            } catch (PermissionDoesNotExist $e) {
            }
        }

        if (is_int($permission)) {
            try {
                $permission = $permissionClass->findById(
                    $permission,
                    $guardName ?? $this->getDefaultGuardName()
                );
            } catch (PermissionDoesNotExist $e) {
            }
        }

        if (! $permission instanceof Permission) {
            return false;
        }
        if ($this->organization) {
            $permissionInScope = Permission::where('scope', '>=', Permissions::SCOPE_ORGANIZATION)
                ->where('id', '=', $permission->id)
                ->count() == 1;
            if (! $permissionInScope) {
                return false;
            }
            /** @var Collection */
            $organizationActivePermissions = $this->organization->getActivePermissions()->pluck('name');
            if (! $organizationActivePermissions->contains($permission->name)) {
                return false;
            }
        } elseif ($this->reseller) {
            $permissionInScope = Permission::where('scope', '>=', Permissions::SCOPE_RESELLER)
                ->where('id', '=', $permission->id)
                ->count() == 1;
            if (! $permissionInScope) {
                return false;
            }
        }

        return $this->defaultCheckPermissionTo($permission, $guardName);
    }
}
