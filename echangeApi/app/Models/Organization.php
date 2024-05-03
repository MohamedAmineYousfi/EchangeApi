<?php

namespace App\Models;

use App\Constants\BillingInformations;
use App\Constants\Permissions;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\ModelIsBillableTo;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\ResellerScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\ModelHasBillingInformations;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\ResellerScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $other_phones
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $phone_extension
 * @property string $phone_type
 * @property string $other_phones
 * @property string $logo
 * @property User $owner
 */
class Organization extends Model implements EventNotifiableContract, ModelIsBillableTo, OnDeleteRelationsCheckable, ResellerScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use LogsActivity;
    use ModelHasBillingInformations {
        \App\Support\Traits\ModelHasBillingInformations::booted as modelHasBillingInformationsBooted;
    }
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use ResellerScoped {
        \App\Support\Traits\ResellerScoped::booted as resellerScopedBooted;
    }
    use SoftDeletes;

    /**
     * The attributes that are assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'excerpt',
        'email',
        'address',
        'phone',
        'phone_extension',
        'phone_type',
        'other_phones',
        'logo',

        ...BillingInformations::MODEL_BILLING_INFORMATIONS_FILLABLES,
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::resellerScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();
        self::modelHasBillingInformationsBooted();

        static::created(function (Organization $organization) {
            /** @var Role */
            $organizationAdminRole = Role::create([
                'name' => $organization->name.' admin',
                'organization_id' => $organization->id,
                'reseller_id' => $organization->reseller->id,
            ]);
            $organizationsPermissions = Permissions::getAllScopePermissions(Permissions::SCOPE_ORGANIZATION);
            $organizationAdminRole->syncPermissions(array_values($organizationsPermissions));

            /** @var array */
            $organizationOtherPhones = $organization->other_phones;
            $owner = new User();
            $owner->active = true;
            $owner->firstname = 'Admin';
            $owner->lastname = $organization->name;
            $owner->email = $organization->email;
            $owner->phone = $organization->phone;
            $owner->phone_extension = $organization->phone;
            $owner->phone_type = $organization->phone_type;
            $owner->other_phones = $organizationOtherPhones;
            $owner->profile_image = $organization->logo;
            $owner->is_staff = false;
            $owner->restrict_to_locations = false;
            $owner->locale = 'fr';
            $owner->password = Hash::make(Str::random(256));
            $owner->organization()->associate($organization);
            $owner->save();

            $owner->assignRole($organizationAdminRole);

            $organization->owner()->associate($owner);
            $organization->save();
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['purchasesInvoices', 'salesInvoices', 'subscriptions', 'users'];
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
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

    public function getObjectName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function scopeName($query, $name)
    {
        return $query->where('organizations.name', 'LIKE', "%$name%", 'and');
    }

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('organizations.name', 'LIKE', "%$search%", 'or');
    }

    /**
     * owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Undocumented function
     */
    public function purchasesInvoices(): HasMany
    {
        return $this->hasMany(PurchasesInvoice::class);
    }

    /**
     * Undocumented function
     */
    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }

    /**
     * Undocumented function
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Undocumented function
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Undocumented function
     */
    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)
            ->whereDate('end_time', '>=', Carbon::now());
    }

    /**
     * get active permissions from the user subscriptions
     */
    public function getActivePermissions(): Collection
    {
        $query = DB::table('permissions')
            ->select('permissions.*')
            ->join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->join('packages', 'packages.default_role_id', '=', 'role_has_permissions.role_id')
            ->join('subscriptions', 'subscriptions.package_id', '=', 'packages.id')
            ->where('subscriptions.organization_id', '=', $this->id)
            ->whereDate('end_time', '>=', Carbon::now());

        return $query->get();
    }
}
