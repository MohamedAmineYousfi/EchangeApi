<?php

namespace App\Models;

use App\Constants\ProductsInformation;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Interfaces\PurchasesDeliverable;
use App\Support\Interfaces\PurchasesInvoiceable;
use App\Support\Interfaces\PurchasesOrderable;
use App\Support\Traits\HasTaxGroups;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property mixed $taxes
 * @property mixed $gallery
 */
class SupplierProduct extends Model implements OnDeleteRelationsCheckable, OrganizationScopable, PurchasesDeliverable, PurchasesInvoiceable, PurchasesOrderable
{
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sku',
        'excerpt',
        'price',
        'taxes',
        'organization_id',
        'product_id',
        'selling_price',
        'buying_price',
        'supplier_code',
        'custom_pricing',
        'custom_taxation',
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

        static::saving(function (SupplierProduct $supplierProduct) {
            if ($supplierProduct->supplier) {
                $supplierProduct->organization()->associate($supplierProduct->supplier->organization);
            }
            if ($supplierProduct->product) {
                $supplierProduct->organization()->associate($supplierProduct->product->organization);
            }
        });

        // this is the only event I found in which the taxgroups can be synched
        static::retrieved(function (SupplierProduct $supplierProduct) {
            if (! $supplierProduct->custom_pricing) {
                $supplierProduct->selling_price = $supplierProduct->product->selling_price;
                $supplierProduct->buying_price = $supplierProduct->product->buying_price;
            }
            if (! $supplierProduct->custom_taxation) {
                $supplierProduct->taxGroups()->sync($supplierProduct->product->taxGroups->pluck('id'));
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

    public function getExcerpt(): string
    {
        return $this->excerpt ?? $this->product->excerpt;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
     * Get the taxes
     */
    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('sku', 'LIKE', "%$search%")
                ->orWhere('supplier_code', 'LIKE', "%$search%")
                ->orWhereHas('supplier', function (Builder $subQuery) use ($search) {
                    $subQuery->where('suppliers.company_name', 'LIKE', "%$search%");
                })
                ->orWhereHas('product', function (Builder $subQuery) use ($search) {
                    $subQuery->where('products.name', 'LIKE', "%$search%")
                        ->orWhere('products.code', 'LIKE', "%$search%")
                        ->orWhere('products.sku', 'LIKE', "%$search%");
                });
        });
    }

    /**
     * @return Builder
     */
    public function scopeStatus(Builder $query, string $value)
    {
        return $query->where(function ($query) use ($value) {
            $query->orWhereHas('product', function (Builder $subQuery) use ($value) {
                $subQuery->where('products.status', $value);
            });
        });
    }

    public function scopeLatestProduct(Builder $query, $value): Builder
    {
        return $query->whereHas('product', function (Builder $subQuery) {
            $subQuery->where('products.created_at', '>', now()->subDays(30))
                ->where('products.status', ProductsInformation::STATUS_ACTIVE);
        });
    }

    public function scopeProduct($query, $productId): Builder
    {
        return $query->where('product_id', '=', $productId);
    }

    public function scopeSupplier($query, $supplierId): Builder
    {
        return $query->where('supplier_id', '=', $supplierId);
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
