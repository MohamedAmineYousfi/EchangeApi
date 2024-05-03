<?php

namespace App\Models;

use App\Support\Classes\BaseInvoiceItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ResellerInvoiceItem extends BaseInvoiceItem
{
    public function getInvoice(): ResellerInvoice
    {
        return $this->resellerInvoice;
    }

    public function getInvoiceable()
    {
        return $this->resellerInvoiceable;
    }

    /**
     * An InvoiceItem belongs to an Invoice
     */
    public function resellerInvoice(): BelongsTo
    {
        return $this->belongsTo(ResellerInvoice::class);
    }

    /**
     * An Event belongs to a User
     */
    public function resellerInvoiceable(): MorphTo
    {
        return $this->morphTo();
    }
}
