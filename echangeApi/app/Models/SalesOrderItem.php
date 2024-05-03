<?php

namespace App\Models;

use App\Support\Classes\BaseOrderItem;
use App\Support\Interfaces\Orderable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalesOrderItem extends BaseOrderItem
{
    /**
     * @return SalesOrder
     */
    public function getOrder()
    {
        return $this->salesOrder;
    }

    public function getOrderable(): Orderable
    {
        return $this->salesOrderable;
    }

    /**
     * An Event belongs to a User
     *
     * @return BelongsTo
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /**
     * An Event belongs to a User
     *
     * @return MorphTo
     */
    public function salesOrderable()
    {
        return $this->morphTo();
    }
}
