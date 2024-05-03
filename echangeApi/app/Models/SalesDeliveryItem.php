<?php

namespace App\Models;

use App\Support\Classes\BaseDeliveryItem;
use App\Support\Interfaces\Deliverable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * {@inheritDoc}
 */
class SalesDeliveryItem extends BaseDeliveryItem
{
    public function getDelivery(): SalesDelivery
    {
        return $this->salesDelivery;
    }

    public function getDeliverable(): Deliverable
    {
        return $this->salesDeliverable;
    }

    /**
     * An DeliveryItem belongs to an Delivery
     */
    public function salesDelivery(): BelongsTo
    {
        return $this->belongsTo(SalesDelivery::class);
    }

    /**
     * An Event belongs to a User
     */
    public function salesDeliverable(): MorphTo
    {
        return $this->morphTo();
    }
}
