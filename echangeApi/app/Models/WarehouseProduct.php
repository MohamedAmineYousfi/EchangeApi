<?php

namespace App\Models;

use App\Constants\ProductsInformation;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Interfaces\PurchasesDeliverable;
use App\Support\Interfaces\PurchasesInvoiceable;
use App\Support\Interfaces\PurchasesOrderable;
use App\Support\Interfaces\SalesDeliverable;
use App\Support\Interfaces\SalesInvoiceable;
use App\Support\Interfaces\SalesOrderable;
use App\Support\Traits\HasTaxGroups;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use CloudCreativity\LaravelJsonApi\Services\JsonApiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property float $selling_price
 * @property float $buying_price
 * @property int $quantity
 * @property int $sku
 * @property int $product_id
 * @property int $warehouse_id
 * @property Product $product
 * @property ?UnitOfMeasureUnit $unitOfMeasureUnit
 */
class WarehouseProduct extends Model implements OnDeleteRelationsCheckable, OrganizationScopable, PurchasesDeliverable, PurchasesInvoiceable, PurchasesOrderable, SalesDeliverable, SalesInvoiceable, SalesOrderable
{
    use HasFactory;
    use HasTaxGroups;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }

    protected $fillable = [
        'quantity',
        'selling_price',
        'buying_price',
        'product_id',
        'warehouse_id',
        'sku',
        'custom_pricing',
        'custom_taxation',
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

        static::creating(function (WarehouseProduct $warehouseProduct) {
            if ($warehouseProduct->product->unitOfMeasure) {
                $warehouseProduct->unitOfMeasureUnit()->associate($warehouseProduct->product->unitOfMeasure->getReferenceUnit());
            }
            $warehouseProduct->organization()->associate($warehouseProduct->warehouse->organization);
        });

        // this is the only event I found in which the taxgroups can be synched
        static::retrieved(function (WarehouseProduct $warehouseProduct) {
            if ($warehouseProduct->unitOfMeasureUnit == null) {
                if ($warehouseProduct->product->unitOfMeasure) {
                    $warehouseProduct->unitOfMeasureUnit()->associate($warehouseProduct->product->unitOfMeasure->getReferenceUnit());
                    $warehouseProduct->save();
                }
            }
            $currentRoute = app(JsonApiService::class)->currentRoute();
            $ressourcetype = json_api()->getDefaultResolver()->getType($currentRoute->getResourceType());
            if ($ressourcetype == WarehouseProduct::class) {
                if (! $warehouseProduct->custom_pricing) {
                    $warehouseProduct->selling_price = $warehouseProduct->product->selling_price;
                    $warehouseProduct->buying_price = $warehouseProduct->product->buying_price;
                }
                if (! $warehouseProduct->custom_taxation) {
                    if ($warehouseProduct->warehouse->use_warehouse_taxes) {
                        $taxGroupsIds = $warehouseProduct->warehouse->taxGroups->pluck('id');
                    } else {
                        $taxGroupsIds = $warehouseProduct->product->taxGroups->pluck('id');
                    }
                    $warehouseProduct->taxGroups()->sync($taxGroupsIds);
                }
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
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function getItemId(): string
    {
        return strval($this->id);
    }

    public function getItem(): Product
    {
        return $this->product;
    }

    public function getProductId(): string
    {
        return strval($this->product->id);
    }

    public function getSku(): string
    {
        return $this->sku ?? $this->product->sku ?? $this->product->code;
    }

    public function getName(): string
    {
        return $this->product->code.' '.$this->product->name;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt ?? $this->product->excerpt;
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unitOfMeasureUnit(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasureUnit::class);
    }

    public function getSellingPrice(): float
    {
        return $this->selling_price;
    }

    public function getBuyingPrice(): float
    {
        return $this->buying_price;
    }

    /**
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('sku', 'LIKE', "%$search%")
                ->orWhereHas('warehouse', function (Builder $subQuery) use ($search) {
                    $subQuery->where('warehouses.name', 'LIKE', "%$search%");
                })
                ->orWhereHas('product', function (Builder $subQuery) use ($search) {
                    $subQuery->where('products.name', 'LIKE', "%$search%")
                        ->orWhere('products.sku', 'LIKE', "%$search%");
                });
        });
    }

    /**
     * @return Builder
     */
    public function scopeStatus(Builder $query, string $value)
    {
        if ($value == ProductsInformation::STATUS_ACTIVE) {
            return $query->where(function ($query) use ($value) {
                $query->orWhereHas('product', function (Builder $subQuery) use ($value) {
                    $subQuery->where('products.status', $value);
                });
            })->orWhere('quantity', '>', 0);
        } else {
            return $query->where(function ($query) use ($value) {
                $query->orWhereHas('product', function (Builder $subQuery) use ($value) {
                    $subQuery->where('products.status', $value);
                });
            });
        }
    }

    public function scopeLatestProduct(Builder $query, $value): Builder
    {
        return $query->whereHas('product', function (Builder $subQuery) {
            $subQuery->where('products.created_at', '>', now()->subDays(30));
        });
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('locations.id', $ids);
    }

    public function scopeProduct($query, $productId): Builder
    {
        return $query->where('product_id', '=', $productId);
    }

    public function scopeProducts($query, $products): Builder
    {
        return $query->whereIn('product_id', $products);
    }

    public function scopeWarehouse($query, $warehouseId): Builder
    {
        return $query->where('warehouse_id', '=', $warehouseId);
    }

    public function scopeCanBeSold($query, $canBeSold): Builder
    {
        return $query->whereHas('product', function ($query) use ($canBeSold) {
            $query->where('can_be_sold', boolval($canBeSold));
        });
    }

    public function scopeCanBePurchases($query, $canBePurchased): Builder
    {
        return $query->whereHas('product', function ($query) use ($canBePurchased) {
            $query->where('can_be_purchased', boolval($canBePurchased));
        });
    }

    public function handleSalesOrderValidated(SalesOrderItem $item): void
    {
    }

    public function handleSalesOrderCanceled(SalesOrderItem $item): void
    {
    }

    public function handleSalesInvoiceValidated(SalesInvoiceItem $item): void
    {
    }

    public function handleSalesInvoicePaied(SalesInvoiceItem $item): void
    {
    }

    public function handleSalesInvoiceCanceled(SalesInvoiceItem $item): void
    {
    }

    public function handleSalesDeliveryValidated(SalesDeliveryItem $item): void
    {
    }

    public function handleSalesDeliveryCanceled(SalesDeliveryItem $item): void
    {
    }

    public function handlePurchasesOrderValidated(PurchasesOrderItem $item): void
    {
    }

    public function handlePurchasesOrderCanceled(PurchasesOrderItem $item): void
    {
    }

    public function handlePurchasesInvoiceValidated(PurchasesInvoiceItem $item): void
    {
    }

    public function handlePurchasesInvoicePaied(PurchasesInvoiceItem $item): void
    {
    }

    public function handlePurchasesInvoiceCanceled(PurchasesInvoiceItem $item): void
    {
    }

    public function handlePurchasesDeliveryValidated(PurchasesDeliveryItem $item): void
    {
    }

    public function handlePurchasesDeliveryCanceled(PurchasesDeliveryItem $item): void
    {
    }
}
