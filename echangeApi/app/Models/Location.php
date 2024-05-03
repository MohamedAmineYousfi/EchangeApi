<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_municipal',
        'excerpt',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::organizationScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();
    }

    public function isLocationRestricted(): bool
    {
        return false;
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['contacts', 'manager', 'users', 'properties', 'auctions', 'customers', 'suppliers', 'products'];
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

    public function scopeSearch($query, $search): Builder
    {
        return $query->where('locations.name', 'LIKE', "%$search%", 'or');
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('locations.id', $ids);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_location');
    }

    /**
     * Get all of the posts that are associated with this location.
     */
    public function properties(): MorphToMany
    {
        return $this->morphedByMany(
            Property::class,
            'model',
            'model_allowed_locations',
            'location_id',
            'model_id'
        );
    }

    /**
     * Get all of the posts that are associated with this location.
     */
    public function auctions(): MorphToMany
    {
        return $this->morphedByMany(
            Auction::class,
            'model',
            'model_allowed_locations',
            'location_id',
            'model_id'
        );
    }

    /**
     * Get all of the posts that are associated with this location.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_allowed_locations',
            'location_id',
            'model_id'
        );
    }

    /**
     * Get all of the posts that are associated with this location.
     */
    public function suppliers(): MorphToMany
    {
        return $this->morphedByMany(
            Supplier::class,
            'model',
            'model_allowed_locations',
            'location_id',
            'model_id'
        );
    }

    /**
     * Get all of the posts that are associated with this location.
     */
    public function customers(): MorphToMany
    {
        return $this->morphedByMany(
            Customer::class,
            'model',
            'model_allowed_locations',
            'location_id',
            'model_id'
        );
    }

    /**
     * Get all of the posts that are associated with this location.
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(
            Product::class,
            'model',
            'model_allowed_locations',
            'location_id',
            'model_id'
        );
    }
}
