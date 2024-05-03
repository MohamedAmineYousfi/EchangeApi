<?php

namespace App\Models;

use App\Support\Classes\BaseOrderItem;
use App\Support\Interfaces\Orderable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchasesOrderItem extends BaseOrderItem
{
    public function getOrder(): PurchasesOrder
    {
        return $this->purchasesOrder;
    }

    public function getOrderable(): Orderable
    {
        return $this->purchasesOrderable;
    }

    /**
     * An Event belongs to a User
     */
    public function purchasesOrder(): BelongsTo
    {
        return $this->belongsTo(PurchasesOrder::class);
    }

    /**
     * An Event belongs to a User
     */
    public function purchasesOrderable(): MorphTo
    {
        return $this->morphTo();
    }
}
