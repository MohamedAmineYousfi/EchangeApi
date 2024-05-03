<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\ResellerScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\ResellerScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ResellerPayment extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, ResellerScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use ResellerScoped {
        \App\Support\Traits\ResellerScoped::booted as resellerScopedBooted;
    }
    use SoftDeletes;

    /************ Abstract functions ************/

    public const SOURCE_MANUAL = 'MANUAL';

    public const SOURCE_STRIPE = 'STRIPE';

    public const SOURCE_PAYPAL = 'PAYPAL';

    public const SOURCE_CASH = 'CASH';

    public const SOURCE_UNKNOWN = 'UNKNOWN';

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELED = 'CANCELED';

    protected $fillable = [
        'date',
        'code',
        'source',
        'status',
        'excerpt',
        'amount',
        'transaction_id',
        'transaction_data',
    ];

    protected $dates = [
        'date',
        'updated_at',
        'created_at',
    ];

    protected $casts = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::resellerScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();

        static::creating(function (ResellerPayment $payment) {
            $payment->refreshPayment();
        });

        static::saved(function (ResellerPayment $payment) {
            $payment->handlePaymentCompleted();
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return [];
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function getObjectName(): string
    {
        return $this->code;
    }

    /**
     * @param  $name
     */
    public function scopeCode($query, $code): Builder
    {
        return $query->where('reseller_payments.code', 'LIKE', "%$code%", 'or');
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeDateBetween($query, $dates): Builder
    {
        return $query->whereBetween('reseller_payments.date', $dates);
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeCreatedAtBetween($query, $dates): Builder
    {
        return $query->whereBetween('reseller_payments.created_at', $dates);
    }

    public function handlePaymentCompleted()
    {
        $this->handleResellerPaymentCompleted();
    }

    /**
     * An InvoiceItem belongs to an Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ResellerInvoice::class, 'reseller_invoice_id');
    }

    public function scopeInvoice($query, $invoice): Builder
    {
        return $query->where('reseller_invoice_id', $invoice);
    }

    /**
     * Get the invoice total amount
     *
     * @return void
     */
    public function refreshPayment()
    {
        if (! $this->code) {
            $this->date = Carbon::now();
            $paymentsCount = ResellerPayment::withoutGlobalScopes()->count() + 1;
            $this->code = 'R-PAY-'.Carbon::now()->format('Ymd').str_pad(strval($paymentsCount), 10, '0', STR_PAD_LEFT);
            $this->reseller()->associate($this->invoice->reseller);
        }
    }

    public function handleResellerPaymentCompleted()
    {
        if ($this->status === self::STATUS_COMPLETED) {
            /** @var ResellerInvoice $invoice */
            $invoice = $this->invoice;
            if (bccomp(strval($invoice->getInvoiceTotalAmount()), strval($invoice->getInvoiceTotalPaied())) == 0) {
                $invoice->handleInvoicePaied();
            }
        }
    }

    public function send()
    {
    }
}
