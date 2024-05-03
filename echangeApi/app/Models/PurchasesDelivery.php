<?php

namespace App\Models;

use App\Support\Classes\BaseDelivery;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchasesDelivery extends BaseDelivery
{
    /**
     * @return void
     */
    public function refreshDelivery()
    {
        if (! $this->code) {
            $deliverysCount = BaseDelivery::withoutGlobalScopes()->count() + 1;
            $this->code = 'P-DEL-'.Carbon::now()->format('Ymd').str_pad(strval($deliverysCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return ?PurchasesOrder
     */
    public function getOrder(): ?PurchasesOrder
    {
        return $this->purchasesOrder;
    }

    /**
     * An Invoice has many InvoiceItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchasesDeliveryItem::class);
    }

    /**
     * @return void
     */
    public function send()
    {
    }

    /**
     * An Invoice is destined to a User
     */
    public function issuer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * An Invoice has many InvoiceItems
     *
     * @return BelongsTo;
     */
    public function purchasesOrder(): BelongsTo
    {
        return $this->belongsTo(PurchasesOrder::class);
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
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

    public function scopePurchasesOrder($query, $purchasesOrder): Builder
    {
        return $query->where('purchases_order_id', $purchasesOrder);
    }
}
