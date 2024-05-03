<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * {@inheritDoc}
 *
 * @property float $quantity
 * @property Product $storable
 * @property UnitOfMeasureUnit $unitOfMeasureUnit
 */
class StockMovementItem extends Model
{
    use LogsActivity;

    protected $fillable = [
        'quantity',
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
        static::creating(function (StockMovementItem $stockMovementItem) {
            if ($stockMovementItem->unitOfMeasureUnit == null) {
                if ($stockMovementItem->storable->unitOfMeasure) {
                    $stockMovementItem->unitOfMeasureUnit()->associate($stockMovementItem->storable->unitOfMeasure->getReferenceUnit());
                }
            }
        });
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * An Event belongs to a User
     */
    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class);
    }

    /**
     * An DeliveryItem belongs to an Delivery
     */
    public function storable(): MorphTo
    {
        return $this->morphTo();
    }

    public function unitOfMeasureUnit(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasureUnit::class);
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query
            ->orWhereHas('product', function (Builder $subQuery) use ($search) {
                $subQuery->where('products.name', 'LIKE', "%$search%", 'or')
                    ->orWhere('products.exceprt', 'LIKE', "%$search%", 'or');
            });
    }
}
