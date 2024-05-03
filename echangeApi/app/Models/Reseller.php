<?php

namespace App\Models;

use App\Constants\Permissions;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $other_phones
 */
class Reseller extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
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
        'config_manager_app_name',
        'config_manager_app_logo',
        'config_manager_url_regex',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();

        static::saved(function ($reseller) {
            $owner = $reseller->owner;
            $owner->reseller()->associate($reseller);
            $owner->save();

            /** @var Role */
            $resellerAdminRole = Role::updateOrCreate([
                'name' => 'Reseller super admin',
                'reseller_id' => $reseller->id,
                'organization_id' => null,
                'guard_name' => config('auth.defaults.guard'),
            ]);
            $resellersPermissions = Permissions::getAllScopePermissions(Permissions::SCOPE_RESELLER);
            $resellerAdminRole->syncPermissions(array_values($resellersPermissions));
            $owner->assignRole($resellerAdminRole);
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['organizations', 'invoices'];
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
        return $this->name;
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
     * owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * owner
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * owner
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(ResellerInvoice::class);
    }

    /**
     * getReseller
     */
    public function getReseller(): Reseller
    {
        return $this;
    }

    public function scopeName($query, $name): Builder
    {
        return $query->where('resellers.name', 'LIKE', "%$name%", 'and');
    }
}
