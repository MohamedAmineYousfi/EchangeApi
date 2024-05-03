<?php

namespace App\Support\Classes;

use App\Constants\Discounts;
use App\Models\User;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\Invoiceable;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $code
 * @property Carbon $expiration_time
 * @property string $excerpt
 * @property string $status
 * @property array<string, mixed> $discounts
 * @property Collection $items
 * @property string $invoicing_status
 * @property string $delivery_status
 * @property string $invoicing_type
 */
abstract class BaseOrder extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
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
    abstract public function refreshOrder();

    abstract public function items();

    abstract public function deliveries();

    abstract public function invoices();

    abstract public function send();

    /************ Common functions, attributes and constants ************/
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_CANCELED = 'CANCELED';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_VALIDATED = 'VALIDATED';

    public const DELIVERY_STATUS_PENDING = 'PENDING';

    public const DELIVERY_STATUS_PARTIALLY_DELIVERED = 'PARTIALLY_DELIVERED';

    public const DELIVERY_STATUS_DELIVERED = 'DELIVERED';

    public const INVOICING_STATUS_PENDING = 'PENDING';

    public const INVOICING_STATUS_PARTIALLY_INVOICED = 'PARTIALLY_INVOICED';

    public const INVOICING_STATUS_INVOICED = 'INVOICED';

    public const INVOICING_TYPE_PRODUCT = 'PRODUCT';

    public const INVOICING_TYPE_AMOUNT = 'AMOUNT';

    protected $fillable = [
        'code',
        'expiration_time',
        'excerpt',
        'status',
        'has_no_taxes',
        'discounts',
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

        static::saving(function (BaseOrder $model) {
            $model->refreshOrder();
        });
    }

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
     * An Order is created by a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /************ scopes  ************/
    /**
     * @param  $name
     * @return mixed
     */
    public function scopeCode($query, $code)
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
     * Get the order total amount
     *
     * @return float
     */
    public function getOrderTotalAmount()
    {
        return $this->getOrderSubTotalAmount() + $this->getOrderTaxes()['total'] - $this->getOrderDiscounts()['total'];
    }

    /**
     * Get the order total amount
     *
     * @return float
     */
    public function getOrderSubTotalAmount()
    {
        $amount = 0;
        foreach ($this->items as $item) {
            /** @var BaseOrderItem $item */
            $amount = $amount + $item->getItemSubTotalAmount();
        }

        return $amount;
    }

    /**
     * Get the order total amount
     *
     * @return array<string, array<int|string, mixed>|float|int>
     */
    public function getOrderTaxes()
    {
        $totalTaxes = 0;
        $calculatedTaxes = [
            'details' => [],
            'total' => 0,
        ];
        foreach ($this->items as $item) {
            /** @var BaseOrderItem $item */
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
     * Get the order total discount amount
     *
     * @return array<string, array<int, mixed>|float|int>
     */
    public function getOrderDiscounts()
    {
        $totalDiscounts = 0;
        $subtotal = $this->getOrderSubTotalAmount();

        /** calculate items discounts */
        $itemsDiscountsAmount = 0;
        foreach ($this->items as $item) {
            /** @var BaseOrderItem $item */
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

        /** calculate order discounts */
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
     * @return array
     */
    public function getDeliveryItemsState()
    {
        $resolver = json_api()->getDefaultResolver();

        /** @var Collection */
        $remainingItems = $orderedItems = $this->items->mapWithKeys(function (BaseOrderItem $item) use ($resolver) {
            return [
                $item->getOrderable()->getItemId() => [
                    'id' => $item->getOrderable()->getItemId(),
                    'item_type' => $resolver->getResourceType(get_class($item->getOrderable())),
                    'code' => $item->getOrderable()->getSku(),
                    'name' => $item->getOrderable()->getName(),
                    'excerpt' => $item->getOrderable()->getExcerpt(),
                    'quantity' => $item->quantity,
                    'product_id' => $item->getOrderable()->getProductId(),
                ],
            ];
        })->toArray();

        /** @var Collection */
        $deliveredItems = $this->items->mapWithKeys(function (BaseOrderItem $item) use ($resolver) {
            return [
                $item->getOrderable()->getItemId() => [
                    'id' => $item->getOrderable()->getItemId(),
                    'item_type' => $resolver->getResourceType(get_class($item->getOrderable())),
                    'code' => $item->getOrderable()->getSku(),
                    'name' => $item->getOrderable()->getName(),
                    'excerpt' => $item->getOrderable()->getExcerpt(),
                    'quantity' => 0,
                ],
            ];
        })->toArray();

        /** @var Collection */
        $deliveriesItems = collect([]);
        $validatedDeliveries = $this->deliveries()->where('status', '=', BaseDelivery::STATUS_VALIDATED)->get();

        foreach ($validatedDeliveries as $delivery) {
            $deliveryItems = $delivery->items->map(function (BaseDeliveryItem $item) use ($resolver) {
                return [
                    'id' => $item->getDeliverable()->getItemId(),
                    'item_type' => $resolver->getResourceType(get_class($item->getDeliverable())),
                    'code' => $item->getDeliverable()->getSku(),
                    'name' => $item->getDeliverable()->getName(),
                    'excerpt' => $item->getDeliverable()->getExcerpt(),
                    'quantity' => $item->quantity,
                ];
            });
            $deliveriesItems = $deliveriesItems->merge($deliveryItems);
        }
        $deliveriesItems = $deliveriesItems->toArray();

        foreach ($deliveriesItems as $item) {
            $itemId = $item['id'];
            $deliveredItems[$itemId]['quantity'] = $deliveredItems[$itemId]['quantity'] + $item['quantity'];
            $remainingItems[$itemId]['quantity'] = $remainingItems[$itemId]['quantity'] - $item['quantity'];
        }

        $orderDelivered = true;
        if (count($deliveriesItems) == 0) {
            $orderDelivered = false;
        }
        foreach ($remainingItems as $item) {
            if ($item['quantity'] > 0) {
                $orderDelivered = false;
            }
        }

        return [
            'orderedItems' => $orderedItems,
            'deliveredItems' => $deliveredItems,
            'remainingItems' => $remainingItems,
            'orderDelivered' => $orderDelivered,
        ];
    }

    /**
     * @return array
     */
    public function getInvoicingItemsState()
    {
        $resolver = json_api()->getDefaultResolver();

        /** @var Collection */
        $remainingItems = $orderedItems = $this->items->mapWithKeys(function (BaseOrderItem $item) use ($resolver) {
            return [
                $item->getOrderable()->getItemId() => [
                    'id' => $item->getOrderable()->getItemId(),
                    'item_type' => $resolver->getResourceType(get_class($item->getOrderable())),
                    'code' => $item->getOrderable()->getSku(),
                    'name' => $item->getOrderable()->getName(),
                    'excerpt' => $item->getOrderable()->getExcerpt(),
                    'quantity' => $item->quantity,
                    'product_id' => $item->getOrderable()->getProductId(),
                ],
            ];
        })->toArray();

        /** @var Collection */
        $invoicedItems = $this->items->mapWithKeys(function (BaseOrderItem $item) use ($resolver) {
            return [
                $item->getOrderable()->getItemId() => [
                    'id' => $item->getOrderable()->getItemId(),
                    'item_type' => $resolver->getResourceType(get_class($item->getOrderable())),
                    'code' => $item->getOrderable()->getSku(),
                    'name' => $item->getOrderable()->getName(),
                    'excerpt' => $item->getOrderable()->getExcerpt(),
                    'quantity' => 0,
                ],
            ];
        })->toArray();

        /** @var Collection */
        $invoicingItems = collect([]);
        $validatedInvoices = $this->invoices()->where('status', '=', BaseInvoice::STATUS_VALIDATED)->get();

        foreach ($validatedInvoices as $invoice) {
            $invoiceItems = $invoice->items
                ->filter(function (BaseInvoiceItem $item) {
                    return $item->getInvoiceable() instanceof Invoiceable;
                })
                ->map(function (BaseInvoiceItem $item) use ($resolver) {
                    return [
                        'id' => $item->getInvoiceable()->getItemId(),
                        'item_type' => $resolver->getResourceType(get_class($item->getInvoiceable())),
                        'code' => $item->getInvoiceable()->getSku(),
                        'name' => $item->getInvoiceable()->getName(),
                        'excerpt' => $item->getInvoiceable()->getExcerpt(),
                        'quantity' => $item->quantity,
                    ];
                });
            $invoicingItems = $invoicingItems->merge($invoiceItems);
        }
        $invoicingItems = $invoicingItems->toArray();

        foreach ($invoicingItems as $item) {
            $itemId = $item['id'];
            $invoicedItems[$itemId]['quantity'] = $invoicedItems[$itemId]['quantity'] + $item['quantity'];
            $remainingItems[$itemId]['quantity'] = $remainingItems[$itemId]['quantity'] - $item['quantity'];
        }

        $orderInvoiced = true;
        if (count($invoicingItems) == 0) {
            $orderInvoiced = false;
        }
        foreach ($remainingItems as $item) {
            if ($item['quantity'] > 0) {
                $orderInvoiced = false;
            }
        }

        return [
            'orderedItems' => $orderedItems,
            'invoicedItems' => $invoicedItems,
            'remainingItems' => $remainingItems,
            'orderInvoiced' => $orderInvoiced,
        ];
    }

    public function getInvoicingAmountsState()
    {
        $orderedAmount = [
            'subtotal' => $this->getOrderSubTotalAmount(),
            'discounts' => $this->getOrderDiscounts()['total'],
            'taxes' => $this->getOrderTaxes()['total'],
            'total' => $this->getOrderTotalAmount(),
        ];
        $orderedTaxesAmounts = $this->getOrderTaxes()['details'];

        $invoices = $this->invoices()->whereIn('status', [BaseInvoice::STATUS_VALIDATED, BaseInvoice::STATUS_PAID])->get();
        $invoicedAmount = [
            'subtotal' => 0,
            'discounts' => 0,
            'taxes' => 0,
            'total' => 0,
        ];
        $invoicedTaxesAmounts = [];

        $remainingTaxesAmount = [];
        $remainingInvoiceAmount = $this->getOrderTotalAmount();

        foreach ($orderedTaxesAmounts as $tax) {
            $invoicedTaxesAmounts[$tax['name']] = [
                'type' => $tax['name'],
                'value' => $tax['value'],
                'name' => $tax['name'],
                'amount' => 0,
            ];
            $remainingTaxesAmount[$tax['name']] = $tax['amount'];
        }

        /** @var BaseInvoice $invoice */
        foreach ($invoices as $invoice) {
            $invoicedAmount['subtotal'] = $invoicedAmount['subtotal'] + $invoice->getInvoiceSubTotalAmount();
            $invoicedAmount['discounts'] = $invoicedAmount['discounts'] + $invoice->getInvoiceDiscounts()['total'];
            $invoicedAmount['taxes'] = $invoicedAmount['taxes'] + $invoice->getInvoiceTaxes()['total'];
            $invoicedAmount['total'] = $invoicedAmount['total'] + $invoice->getInvoiceTotalAmount();

            foreach ($invoice->getInvoiceTaxes()['details'] as $tax) {
                $invoicedTaxesAmounts[$tax['name']] = [
                    'amount' => (
                        isset($invoicedTaxesAmounts[$tax['name']])
                        ? $invoicedTaxesAmounts[$tax['name']]['amount'] + $tax['amount']
                        : $tax['amount']
                    ),
                    //'amount' => $invoicedTaxesAmounts[$tax['name']]['amount'] + $tax['amount'],
                ];
            }
        }

        $remainingInvoiceAmount = $remainingInvoiceAmount - $invoicedAmount['total'];
        foreach ($remainingTaxesAmount as $key => $tax) {
            $remainingTaxesAmount[$key] = $remainingTaxesAmount[$key] - $invoicedTaxesAmounts[$key]['amount'];
        }

        return [
            'orderedAmount' => $orderedAmount,
            'invoicedAmount' => $invoicedAmount,
            'invoicedTaxesAmounts' => $invoicedTaxesAmounts,
            'orderedTaxesAmounts' => $orderedTaxesAmounts,
            'remainingInvoiceAmount' => $remainingInvoiceAmount,
            'remainingTaxesAmount' => $remainingTaxesAmount,
            'orderInvoiced' => bccomp(strval($remainingInvoiceAmount), '0', 2) == 0,
        ];
    }

    public function getInvoicingStatus()
    {
        if ($this->invoicing_type == BaseOrder::INVOICING_TYPE_AMOUNT) {
            $invoicingAmountsState = $this->getInvoicingAmountsState();
            if ($invoicingAmountsState['orderInvoiced']) {
                return BaseOrder::INVOICING_STATUS_INVOICED;
            } else {
                if ($invoicingAmountsState['remainingInvoiceAmount'] > 0) {
                    return BaseOrder::INVOICING_STATUS_PARTIALLY_INVOICED;
                }
            }
        } elseif ($this->invoicing_type == BaseOrder::INVOICING_TYPE_PRODUCT) {
            $invoicingItemsState = $this->getInvoicingItemsState();
            if ($invoicingItemsState['orderInvoiced']) {
                return BaseOrder::INVOICING_STATUS_INVOICED;
            } else {
                $invoicedItems = array_filter($invoicingItemsState['invoicedItems'], function ($item) {
                    return $item['quantity'] > 0;
                });
                if (count($invoicedItems) > 0) {
                    return BaseOrder::INVOICING_STATUS_PARTIALLY_INVOICED;
                }
            }
        }

        return BaseOrder::INVOICING_STATUS_PENDING;
    }

    public function getDeliveryStatus()
    {
        $deliveryItemsState = $this->getDeliveryItemsState();
        if ($deliveryItemsState['orderDelivered']) {
            return BaseOrder::DELIVERY_STATUS_DELIVERED;
        } else {
            $deliveredItems = array_filter($deliveryItemsState['deliveredItems'], function ($item) {
                return $item['quantity'] > 0;
            });
            if (count($deliveredItems) > 0) {
                return BaseOrder::DELIVERY_STATUS_PARTIALLY_DELIVERED;
            }
        }

        return BaseOrder::DELIVERY_STATUS_PENDING;
    }

    /**
     * @return void
     */
    public function setOrderStatus()
    {
        $this->invoicing_status = $this->getInvoicingStatus();
        $this->delivery_status = $this->getDeliveryStatus();

        if (
            ($this->invoicing_status == BaseOrder::INVOICING_STATUS_INVOICED)
            && ($this->delivery_status == BaseOrder::DELIVERY_STATUS_DELIVERED)
        ) {
            $this->status = BaseOrder::STATUS_COMPLETED;
        }

        $this->save();
    }
}
