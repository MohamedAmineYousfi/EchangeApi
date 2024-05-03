<?php

namespace App\Models;

use App\Constants\ProductsInformation;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Interfaces\PurchasesDeliverable;
use App\Support\Interfaces\PurchasesInvoiceable;
use App\Support\Interfaces\PurchasesOrderable;
use App\Support\Interfaces\SalesDeliverable;
use App\Support\Interfaces\SalesInvoiceable;
use App\Support\Interfaces\SalesOrderable;
use App\Support\Interfaces\TaxableItem;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\HasTaxGroups;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * {@inheritDoc}
 *
 * @property mixed $gallery
 * @property float $amount
 * @property ?UnitOfMeasure $unitOfMeasure
 */
class Product extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable, PurchasesDeliverable, PurchasesInvoiceable, PurchasesOrderable, SalesDeliverable, SalesInvoiceable, SalesOrderable, TaxableItem
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use HasTaxGroups;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
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
        'code',
        'sku',
        'name',
        'excerpt',
        'selling_price',
        'buying_price',
        'picture',
        'gallery',
        'organization_id',
        'status',
        'custom_pricing',
        'custom_taxation',
        'can_be_sold',
        'can_be_purchased',
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
        self::eventNotifiableBooted();

        static::saving(function (Product $product) {
            if (! $product->code) {
                $invoicesCount = Product::withoutGlobalScopes()->count() + 1;
                $product->code = 'PRD-'.Carbon::now()->format('Ymd').str_pad(strval($invoicesCount), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['purchasesInvoiceItems', 'salesInvoiceItems'];
    }

    public function getItemId(): string
    {
        return strval($this->id);
    }

    public function getItem(): Product
    {
        return $this;
    }

    public function getProductId(): string
    {
        return strval($this->id);
    }

    public function getObjectName(): string
    {
        return $this->code.' '.$this->name;
    }

    public function getSku(): string
    {
        return $this->sku ?? $this->code;
    }

    public function getName(): string
    {
        return $this->code.' '.$this->name;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt ?? ' ';
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
     * Undocumented function
     */
    public function purchasesInvoiceItems(): MorphMany
    {
        return $this->morphMany(PurchasesInvoiceItem::class, 'purchases_invoiceable');
    }

    /**
     * Undocumented function
     */
    public function salesInvoiceItems(): MorphMany
    {
        return $this->morphMany(SalesInvoiceItem::class, 'sales_invoiceable');
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    public function supplierProducts(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function warehouseProducts(): HasMany
    {
        return $this->hasMany(WarehouseProduct::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class);
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where('products.name', 'LIKE', "%$search%", 'or')
            ->where('products.code', 'LIKE', "%$search%", 'or')
            ->where('products.sku', 'LIKE', "%$search%", 'or');
    }

    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('products.id', $ids);
    }

    public function scopeIdsNotIn(Builder $query, ?array $ids): Builder
    {
        return $query->whereNotIn('products.id', $ids);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->where('products.status', $status);
    }

    /**
     * @param  Builder  $query
     * @param  string  $warehouse_id
     */
    public function scopeWarehouse($query, $warehouse_id): Builder
    {
        return $query->whereHas('warehouseProducts', function ($subQuery) use ($warehouse_id) {
            $subQuery->where('warehouse_products.warehouse_id', '=', $warehouse_id);
        });
    }

    public function scopeProductsNotInWarehouse(Builder $query, string $warehouse_id): Builder
    {
        $productIds = WarehouseProduct::where('warehouse_id', $warehouse_id)->pluck('product_id')->toArray();

        return $query->whereNotIn('products.id', $productIds);
    }

    public function scopeProductsNotInSupplier(Builder $query, string $supplier_id): Builder
    {
        $productIds = SupplierProduct::where('supplier_id', $supplier_id)->pluck('product_id')->toArray();

        return $query->whereNotIn('products.id', $productIds);
    }

    /**
     * @param  Builder  $query
     * @param  string  $supplier_id
     */
    public function scopeSupplier($query, $supplier_id): Builder
    {
        return $query->whereHas('supplierProducts', function ($subQuery) use ($supplier_id) {
            $subQuery->where('supplier_products.supplier_id', '=', $supplier_id);
        });
    }

    /**
     * @param  Builder  $query
     * @param  string  $supplier_id
     */
    public function scopeSupplierProduct($query, $supplier_id): Builder
    {
        return $query->whereHas('supplierProducts', function ($subQuery) use ($supplier_id) {
            $subQuery->where('supplier_products.supplier_id', '=', $supplier_id);
        });
    }

    public function scopeLatestProduct(Builder $query): Builder
    {
        return $query->where('created_at', '>', now()->subDays(30))
            ->where('status', ProductsInformation::STATUS_ACTIVE);
    }

    public function scopeHierarchicalCategoryAssociation(Builder $query, array $categoryIds): Builder
    {
        $allCategoryIds = $categoryIds;

        foreach ($categoryIds as $categoryId) {
            $category = Category::find($categoryId);
            while ($category->parent_id !== null) {
                $category = Category::find($category->parent_id);
                $allCategoryIds[] = $category->id;
            }
        }

        $allCategoryIds = array_unique($allCategoryIds);

        return $query->whereHas('categories', function ($query) use ($allCategoryIds) {
            $query->whereIn('category_id', $allCategoryIds);
        });
    }

    public function scopeInCategories(Builder $query, array $categoryIds): Builder
    {
        $allCategoryIds = $categoryIds;

        foreach ($categoryIds as $categoryId) {
            $subCategories = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
            $allCategoryIds = array_merge($allCategoryIds, $subCategories);
        }

        $allCategoryIds = array_unique($allCategoryIds);

        return $query->whereHas('categories', function ($query) use ($allCategoryIds) {
            $query->whereIn('category_id', $allCategoryIds);
        });
    }

    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        $category = Category::with('subCategories')->find($categoryId);

        $allCategories = collect([$category]);

        $subCategories = $category->subCategories;

        while (! $subCategories->isEmpty()) {
            $allCategories = $allCategories->merge($subCategories);
            $subCategoriesIds = $subCategories->pluck('id')->toArray();
            $subCategories = Category::whereIn('parent_id', $subCategoriesIds)->get();
        }

        $allCategoryIds = $allCategories->pluck('id')->toArray();

        return $query->whereHas('categories', function ($query) use ($allCategoryIds) {
            $query->whereIn('category_id', array_unique($allCategoryIds));
        });
    }

    public function scopeCanBePurchased(Builder $query, $canBePurchased): Builder
    {
        return $query->where('can_be_purchased', boolval($canBePurchased));
    }

    public function scopeCanBeSold(Builder $query, $canBeSold): Builder
    {
        return $query->where('can_be_sold', boolval($canBeSold));
    }

    public function getDenomination(): string
    {
        return "$this->code - $this->name";
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
