<?php

namespace App\Models;

use App\Constants\BillingInformations;
use App\Support\Classes\BaseOrder;
use App\Support\Interfaces\ModelIsBillableTo;
use App\Support\Traits\ModelHasBillingInformations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalesOrder extends BaseOrder implements ModelIsBillableTo
{
    use ModelHasBillingInformations {
        \App\Support\Traits\ModelHasBillingInformations::booted as modelHasBillingInformationsBooted;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();
        self::modelHasBillingInformationsBooted();
    }

    protected $fillable = [
        'code',
        'expiration_time',
        'excerpt',
        'status',
        'discounts',
        'has_no_taxes',

        ...BillingInformations::MODEL_BILLING_INFORMATIONS_FILLABLES,
    ];

    /**
     * @return void
     */
    public function refreshOrder()
    {
        if (! $this->code) {
            $ordersCount = BaseOrder::withoutGlobalScopes()->count() + 1;
            $this->code = 'S-ORD-'.Carbon::now()->format('Ymd').str_pad(strval($ordersCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * An Order has many OrderItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    /**
     * @return void
     */
    public function send()
    {
    }

    /**
     * An Order is destined to a User
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }

    /**
     * An Order has many OrderItems
     *
     * @return HasMany;
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(SalesDelivery::class);
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
}
