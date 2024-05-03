<?php

namespace App\Models;

use App\Support\Classes\BaseOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchasesOrder extends BaseOrder
{
    /**
     * @return void
     */
    public function refreshOrder()
    {
        if (! $this->code) {
            $ordersCount = BaseOrder::withoutGlobalScopes()->count() + 1;
            $this->code = 'P-ORD-'.Carbon::now()->format('Ymd').str_pad(strval($ordersCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * An Order has many OrderItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchasesOrderItem::class);
    }

    /**
     * @return void
     */
    public function send()
    {
    }

    /**
     * An Order is destined to a User
     */
    public function issuer(): MorphTo
    {
        return $this->morphTo();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(PurchasesInvoice::class);
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * An Order has many OrderItems
     *
     * @return HasMany;
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(PurchasesDelivery::class);
    }

    public function scopeIssuer($query, $issuer): Builder
    {
        return $query->where('issuer_id', $issuer);
    }

    /**
     * @param  $issuer
     */
    public function scopeIssuerType($query, $issuerType): Builder
    {
        $type = json_api()->getDefaultResolver()->getType($issuerType);

        return $query->where('issuer_type', $type);
    }

    /**
     * @param  $issuer
     */
    public function scopeIssuerId($query, $issuerId): Builder
    {
        return $query->where('issuer_id', $issuerId);
    }
}
