<?php

namespace App\Models;

use App\Constants\DeliveryInformations;
use App\Support\Classes\BaseDelivery;
use App\Support\Interfaces\ModelIsDeliverableTo;
use App\Support\Traits\ModelHasDeliveryInformations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalesDelivery extends BaseDelivery implements ModelIsDeliverableTo
{
    use ModelHasDeliveryInformations {
        \App\Support\Traits\ModelHasDeliveryInformations::booted as modelHasDeliveryInformationsBooted;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();
        self::modelHasDeliveryInformationsBooted();
    }

    /**
     * @return void
     */
    public function refreshDelivery()
    {
        if (! $this->code) {
            $deliverysCount = BaseDelivery::withoutGlobalScopes()->count() + 1;
            $this->code = 'S-DEL-'.Carbon::now()->format('Ymd').str_pad(strval($deliverysCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @return ?SalesOrder
     */
    public function getOrder(): ?SalesOrder
    {
        return $this->salesOrder;
    }

    /**
     * An Delivery has many DeliveryItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesDeliveryItem::class);
    }

    /**
     * Send delivery
     */
    public function send()
    {
    }

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'expiration_time',
        'excerpt',
        'status',

        ...DeliveryInformations::MODEL_DELIVERY_INFORMATIONS_FILLABLES,
    ];

    /**
     * An Delivery is destined to a User
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * An Delivery has many DeliveryItems
     *
     * @return BelongsTo;
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeRecipient($query, $recipient): Builder
    {
        return $query->where('recipient_id', $recipient);
    }

    /**
     * @param  $recipient
     */
    public function scopeRecipientType($query, $recipientType): Builder
    {
        $type = json_api()->getDefaultResolver()->getType($recipientType);

        return $query->where('recipient_type', $type);
    }

    /**
     * @param  $recipient
     */
    public function scopeRecipientId($query, $recipientId): Builder
    {
        return $query->where('recipient_id', $recipientId);
    }

    public function scopeSalesOrder($query, $salesOrder): Builder
    {
        return $query->where('sales_order_id', $salesOrder);
    }
}
