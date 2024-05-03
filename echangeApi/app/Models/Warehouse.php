<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\HasTaxGroups;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
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
 * @property array<string, mixed> $taxes
 */
class Warehouse extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use HasTaxGroups;
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
        'excerpt',
        'allow_negative_inventory',
        'allow_unregistered_products',
        'organization_id',
        'model_used',
        'is_model',
        'applied_at',
        'use_warehouse_taxes',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'applied_at',
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

        static::retrieved(function (Warehouse $warehouse) {
            if (! $warehouse->use_warehouse_taxes) {
                $warehouse->taxGroups()->detach();
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return [];
    }

    /**
     * Get the taxes
     */
    protected function taxes(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
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

    public function warehouseProducts(): HasMany
    {
        return $this->hasMany(WarehouseProduct::class);
    }

    public function modelUsed(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'model_used');
    }

    public function usedBy(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'model_used');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('warehouses.name', 'LIKE', "%$search%", 'or')
            ->orWhere('warehouses.excerpt', 'LIKE', "%$search%", 'or');
    }

    public function scopeIdsNotIn(Builder $query, ?array $ids): Builder
    {
        return $query->whereNotIn('warehouses.id', $ids);
    }

    public function scopeModelUsed($query, $modelUsedId)
    {
        return $query->where('model_used', $modelUsedId);
    }

    public function scopeIsModel($query, $isModel)
    {
        return $query->where('is_model', filter_var($isModel, FILTER_VALIDATE_BOOLEAN));
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('warehouses.id', $ids);
    }
}
