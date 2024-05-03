<?php

namespace App\Models;

use App\Support\Classes\BaseDeliveryItem;
use App\Support\Interfaces\Deliverable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PurchasesDeliveryItem extends BaseDeliveryItem
{
    public function getDelivery(): PurchasesDelivery
    {
        return $this->purchasesDelivery;
    }

    public function getDeliverable(): Deliverable
    {
        return $this->purchasesDeliverable;
    }

    /**
     * An DeliveryItem belongs to an Delivery
     */
    public function purchasesDelivery(): BelongsTo
    {
        return $this->belongsTo(PurchasesDelivery::class);
    }

    /**
     * An Event belongs to a User
     */
    public function purchasesDeliverable(): MorphTo
    {
        return $this->morphTo();
    }
}
