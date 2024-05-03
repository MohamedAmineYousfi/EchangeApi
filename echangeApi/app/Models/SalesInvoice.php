<?php

namespace App\Models;

use App\Constants\BillingInformations;
use App\Support\Classes\BaseInvoice;
use App\Support\Interfaces\ModelIsBillableTo;
use App\Support\Interfaces\SalesInvoiceable;
use App\Support\Traits\ModelHasBillingInformations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property bool $has_no_taxesdiscounts.
 */
class SalesInvoice extends BaseInvoice implements ModelIsBillableTo
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

    /**
     * Get the invoice total amount
     *
     * @return void
     */
    public function refreshInvoice()
    {
        if (! $this->code) {
            $invoicesCount = BaseInvoice::withoutGlobalScopes()->count() + 1;
            $this->code = 'S-INV-'.Carbon::now()->format('Ymd').str_pad(strval($invoicesCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * An Invoice has many InvoiceItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    /**
     * An Invoice has many Payments
     *
     * @return HasMany;
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SalesPayment::class);
    }

    /**
     * Send invoice
     */
    public function send()
    {
    }

    /**
     * @return ?SalesOrder;
     */
    public function getOrder(): ?SalesOrder
    {
        return $this->salesOrder;
    }

    public function handleInvoicePaied()
    {
        $this->status = SalesInvoice::STATUS_PAID;
        $this->save();

        /** when invoice is paied mark handle all invoices items as paied */
        /** @var SalesInvoiceItem $item */
        foreach ($this->items as $item) {
            $invoiceable = $item->getInvoiceable();
            if ($invoiceable instanceof SalesInvoiceable) {
                $invoiceable->handleSalesInvoicePaied($item);
            }
        }

        $this->send();
    }

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'expiration_time',
        'excerpt',
        'status',
        'has_no_taxes',
        'discounts',

        ...BillingInformations::MODEL_BILLING_INFORMATIONS_FILLABLES,
    ];

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    /**
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
