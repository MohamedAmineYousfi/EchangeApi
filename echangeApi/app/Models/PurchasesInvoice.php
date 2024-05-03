<?php

namespace App\Models;

use App\Support\Classes\BaseInvoice;
use App\Support\Interfaces\PurchasesInvoiceable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchasesInvoice extends BaseInvoice
{
    /**
     * @return void
     */
    public function refreshInvoice()
    {
        if (! $this->code) {
            $invoicesCount = BaseInvoice::withoutGlobalScopes()->count() + 1;
            $this->code = 'P-INV-'.Carbon::now()->format('Ymd').str_pad(strval($invoicesCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * An Invoice has many InvoiceItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchasesInvoiceItem::class);
    }

    /**
     * An Invoice has many Payments
     *
     * @return HasMany;
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PurchasesPayment::class);
    }

    /**
     * @return ?PurchasesOrder;
     */
    public function getOrder(): ?PurchasesOrder
    {
        return $this->purchasesOrder;
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function handleInvoicePaied()
    {
        $this->status = PurchasesInvoice::STATUS_PAID;
        $this->save();

        /** when invoice is paied mark handle all invoices items as paied */
        /** @var PurchasesInvoiceItem $item */
        foreach ($this->items as $item) {
            $invoiceable = $item->getInvoiceable();
            if ($invoiceable instanceof PurchasesInvoiceable) {
                $invoiceable->handlePurchasesInvoicePaied($item);
            }
        }

        $this->send();
    }

    /**
     * Send invoice
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
