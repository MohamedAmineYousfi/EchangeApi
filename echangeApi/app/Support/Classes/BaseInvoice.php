<?php

namespace App\Support\Classes;

use App\Constants\Discounts;
use App\Models\User;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property Collection $payments
 * @property Collection $items
 * @property array<string, mixed> $discounts
 * @property string $status
 * @property string $code
 * @property string $invoice_type
 * @property bool $has_no_taxes
 */
abstract class BaseInvoice extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use SoftDeletes;

    /************ Abstract functions ************/
    abstract public function refreshInvoice();

    abstract public function items();

    abstract public function payments();

    abstract public function getOrder();

    abstract public function handleInvoicePaied();

    abstract public function send();

    /************ Common functions, attributes and constants ************/
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_PAID = 'PAID';

    public const STATUS_CANCELED = 'CANCELED';

    public const STATUS_VALIDATED = 'VALIDATED';

    protected $fillable = [
        'code',
        'expiration_time',
        'excerpt',
        'status',
        'has_no_taxes',
        'discounts',
        'invoice_type',
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
        self::organizationScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();

        static::saving(function (BaseInvoice $model) {
            if (! $model->invoice_type) {
                $model->invoice_type = BaseOrder::INVOICING_TYPE_PRODUCT;
            }
            $model->refreshInvoice();
        });
        self::saved(function (BaseInvoice $invoice) {
            /** @var BaseOrder */
            $order = $invoice->getOrder();
            if ($order != null) {
                if ($order->invoicing_type == null) {
                    $order->invoicing_type = BaseOrder::INVOICING_TYPE_PRODUCT;
                    $order->save();
                }
                $invoice->getOrder()->setOrderStatus();
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return [];
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
            /** @var BaseInvoiceItem $item */
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
            /** @var BaseInvoiceItem $item */
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
            /** @var BaseInvoiceItem $item */
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
            /** @var BasePayment $item */
            if ($item->status === BasePayment::STATUS_COMPLETED) {
                $amount = $amount + $item->amount;
            }
        }

        return $amount;
    }
}
