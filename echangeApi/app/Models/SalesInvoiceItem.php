<?php

namespace App\Models;

use App\Support\Classes\BaseInvoiceItem;
use App\Support\Interfaces\Invoiceable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalesInvoiceItem extends BaseInvoiceItem
{
    /**
     * @return SalesInvoice
     */
    public function getInvoice()
    {
        return $this->salesInvoice;
    }

    public function getInvoiceable(): ?Invoiceable
    {
        return $this->salesInvoiceable;
    }

    /**
     * An InvoiceItem belongs to an Invoice
     */
    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    /**
     * An Event belongs to a User
     */
    public function salesInvoiceable(): MorphTo
    {
        return $this->morphTo();
    }
}
