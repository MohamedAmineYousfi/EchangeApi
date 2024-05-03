<?php

namespace App\Models;

use App\Constants\BillingInformations;
use App\Constants\Discounts;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\ModelIsBillableTo;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\ResellerInvoiceable;
use App\Support\Interfaces\ResellerScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\ModelHasBillingInformations;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\ResellerScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property mixed $discounts
 */
class ResellerInvoice extends Model implements EventNotifiableContract, ModelIsBillableTo, OnDeleteRelationsCheckable, ResellerScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity;
    use ModelHasBillingInformations {
        \App\Support\Traits\ModelHasBillingInformations::booted as modelHasBillingInformationsBooted;
    }
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use ResellerScoped {
        \App\Support\Traits\ResellerScoped::booted as resellerScopedBooted;
    }
    use SoftDeletes;

    /************ Common functions, attributes and constants ************/
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_PAID = 'PAID';

    public const STATUS_CANCELED = 'CANCELED';

    public const STATUS_VALIDATED = 'VALIDATED';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'expiration_time',
        'excerpt',
        'status',
        'discounts',

        ...BillingInformations::MODEL_BILLING_INFORMATIONS_FILLABLES,
    ];

    protected $dates = [
        'expiration_time',
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
        self::modelHasBillingInformationsBooted();

        static::saving(function (ResellerInvoice $model) {
            $model->refreshInvoice();
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['items'];
    }

    public function getObjectName(): string
    {
        return $this->code;
    }

    /**
     * Get the discounts
     */
    protected function discounts(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * An Invoice is created by a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /************ scopes  ************/
    /**
     * @param  $name
     */
    public function scopeCode($query, $code): Builder
    {
        return $query->where('code', 'LIKE', "%$code%", 'and');
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeCreatedAtBetween($query, $dates): Builder
    {
        return $query->whereBetween('created_at', $dates);
    }

    /************ computing functions  ************/
    /**
     * Get the invoice total amount
     *
     * @return float
     */
    public function getInvoiceTotalAmount()
    {
        return $this->getInvoiceSubTotalAmount() + $this->getInvoiceTaxes()['total'] - $this->getInvoiceDiscounts()['total'];
    }

    /**
     * Get the invoice total amount
     *
     * @return float
     */
    public function getInvoiceSubTotalAmount()
    {
        $amount = 0;
        foreach ($this->items as $item) {
            /** @var ResellerInvoiceItem $item */
            $amount = $amount + $item->getItemSubTotalAmount();
        }

        return $amount;
    }

    /**
     * Get the invoice total amount
     *
     * @return array<string, array<int, array<string, mixed>>|float|int>
     */
    public function getInvoiceTaxes()
    {
        $totalTaxes = 0;
        $calculatedTaxes = [
            'details' => [],
            'total' => 0,
        ];
        foreach ($this->items as $item) {
            /** @var ResellerInvoiceItem $item */
            $itemTaxes = $item->getItemTaxes();
            foreach ($itemTaxes['details'] as $itemTaxLine) {
                $taxName = $itemTaxLine['name'];
                $taxAmount = $itemTaxLine['amount'];
                if (isset($calculatedTaxes['details'][$taxName])) {
                    $calculatedTaxes['details'][$taxName]['amount'] = $calculatedTaxes['details'][$taxName]['amount'] + $taxAmount;
                } else {
                    $calculatedTaxes['details'][$taxName] = $itemTaxLine;
                }
                $totalTaxes = $totalTaxes + $itemTaxLine['amount'];
            }
        }
        $calculatedTaxes['total'] = $totalTaxes;

        return $calculatedTaxes;
    }

    /**
     * Get the invoice total amount
     *
     * @return array<string, array<int, array<string, mixed>>|float|int>
     */
    public function getInvoiceDiscounts()
    {
        $totalDiscounts = 0;
        $subtotal = $this->getInvoiceSubTotalAmount();

        /** calculate items discounts */
        $itemsDiscountsAmount = 0;
        foreach ($this->items as $item) {
            /** @var ResellerInvoiceItem $item */
            $itemsDiscountsAmount = $itemsDiscountsAmount + $item->getItemDiscountAmount();
        }
        $itemsDiscountsValue = $subtotal != 0 ? ($itemsDiscountsAmount / $subtotal) * 100 : 0;

        /** init calculated discounts */
        $calculatedDiscounts = [
            'details' => [
                [
                    'name' => 'ITEMS_DISCOUNTS',
                    'type' => Discounts::TYPE_PERCENTAGE,
                    'amount' => $itemsDiscountsAmount,
                    'value' => $itemsDiscountsValue,
                ],
            ],
            'total' => 0,
        ];
        $totalDiscounts = $itemsDiscountsAmount;

        /** calculate invoice discounts */
        foreach ($this->discounts as $discount) {
            $discountAmount = 0;
            if ($discount['type'] == Discounts::TYPE_AMOUNT) {
                $discountAmount = $discount['value'];
            } elseif ($discount['type'] == Discounts::TYPE_PERCENTAGE) {
                $discountAmount = ($subtotal * ($discount['value'] / 100));
            }
            $discount['amount'] = $discountAmount;
            $totalDiscounts = $totalDiscounts + $discountAmount;
            $calculatedDiscounts['details'][] = $discount;
        }
        $calculatedDiscounts['total'] = $totalDiscounts;

        return $calculatedDiscounts;
    }

    /**
     * Get the invoice total amount
     *
     * @return float
     */
    public function getInvoiceTotalPaied()
    {
        $amount = 0;
        foreach ($this->payments as $item) {
            /** @var ResellerPayment $item */
            if ($item->status === ResellerPayment::STATUS_COMPLETED) {
                $amount = $amount + $item->amount;
            }
        }

        return $amount;
    }

    /**
     * Get the invoice total amount
     *
     * @return void
     */
    public function refreshInvoice()
    {
        if (! $this->code) {
            $invoicesCount = ResellerInvoice::withoutGlobalScopes()->count() + 1;
            $this->code = 'R-INV-'.Carbon::now()->format('Ymd').str_pad(strval($invoicesCount), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * An Invoice has many InvoiceItems
     *
     * @return HasMany;
     */
    public function items(): HasMany
    {
        return $this->hasMany(ResellerInvoiceItem::class);
    }

    /**
     * An Invoice has many Payments
     *
     * @return HasMany;
     */
    public function payments(): HasMany
    {
        return $this->hasMany(ResellerPayment::class);
    }

    /**
     * Send invoice
     */
    public function send()
    {
    }

    public function handleInvoicePaied()
    {
        $this->status = ResellerInvoice::STATUS_PAID;
        $this->save();

        /** when invoice is paied mark handle all invoices items as paied */
        foreach ($this->items as $item) {
            $invoiceable = $item->getInvoiceable();
            /** @var ResellerInvoiceItem $item */
            if ($invoiceable instanceof ResellerInvoiceable) {
                $invoiceable->handleResellerInvoicePaied($item);
            }
        }

        $this->send();
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeRecipient($query, $recipient): Builder
    {
        return $query->where('organization_id', $recipient);
    }
}
