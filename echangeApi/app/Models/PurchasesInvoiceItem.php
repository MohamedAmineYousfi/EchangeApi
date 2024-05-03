<?php

namespace App\Models;

use App\Support\Classes\BaseInvoiceItem;
use App\Support\Interfaces\Invoiceable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchasesInvoiceItem extends BaseInvoiceItem
{
    public function getInvoice(): PurchasesInvoice
    {
        return $this->purchasesInvoice;
    }

    public function getInvoiceable(): ?Invoiceable
    {
        return $this->purchasesInvoiceable;
    }

    /**
     * An InvoiceItem belongs to an Invoice
     */
    public function purchasesInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchasesInvoice::class);
    }

    /**
     * An Event belongs to a User
     */
    public function purchasesInvoiceable(): MorphTo
    {
        return $this->morphTo();
    }
}
