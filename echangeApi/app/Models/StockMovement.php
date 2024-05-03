<?php

namespace App\Models;

use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $movement_type
 * @property string $status
 * @property string $code
 * @property ?string $excerpt
 */
class StockMovement extends Model implements OnDeleteRelationsCheckable, OrganizationScopable
{
    use HasFactory;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use SoftDeletes;

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_CANCELED = 'CANCELED';

    public const STATUS_VALIDATED = 'VALIDATED';

    public const TYPE_ADD = 'ADD';

    public const TYPE_REMOVE = 'REMOVE';

    public const TYPE_MOVE = 'MOVE';

    public const TYPE_CORRECT = 'CORRECT';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'movement_type',
        'excerpt',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::organizationScopedBooted();
        self::onDeleteRelationsCheckedBooted();

        static::saving(function (StockMovement $model) {
            $model->refreshStockMovement();
        });
    }

    /**
     * @return void
     */
    public function refreshStockMovement()
    {
        if (! $this->code) {
            $ordersCount = StockMovement::withoutGlobalScopes()->count() + 1;
            $this->code = 'SMV-'.Carbon::now()->format('Ymd').str_pad(strval($ordersCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return [];
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function triggerObject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * An Invoice has many InvoiceItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockMovementItem::class);
    }

    public function scopeSourceWarehouse($query, $warehouseId): Builder
    {
        return $query->where('source_warehouse_id', '=', $warehouseId);
    }

    public function scopeDestinationWarehouse($query, $warehouseId): Builder
    {
        return $query->where('destination_warehouse_id', '=', $warehouseId);
    }
}
