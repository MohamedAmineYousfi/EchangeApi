<?php

namespace App\Support\Classes;

use App\Support\Interfaces\TaxableItem;
use App\Support\Traits\HasTaxGroups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $code
 * @property string $excerpt
 * @property float $unit_price
 * @property int $quantity
 * @property float $discount
 * @property array<string, mixed> $taxes
 */
abstract class BaseInvoiceItem extends Model implements TaxableItem
{
    use HasTaxGroups;
    use LogsActivity;
    use SoftDeletes;

    /************ Abstract functions ************/
    abstract public function getInvoice();

    abstract public function getInvoiceable();

    /************ Common functions, attributes and constants ************/

    protected $fillable = [
        'code',
        'excerpt',
        'unit_price',
        'quantity',
        'discount',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [];

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /************ computing functions  ************/

    /**
     * Get the invoice total amount
     *
     * @return float
     */
    public function getItemTotalAmount()
    {
        return $this->getItemSubTotalAmount() - $this->getItemDiscountAmount() + $this->getItemTaxes()['total'];
    }

    /**
     * Get the invoice total amount
     *
     * @return float
     */
    public function getItemSubTotalAmount()
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get the invoice total amount
     */
    public function getTaxableBaseAmount(): float
    {
        return $this->getItemSubTotalAmount() - $this->getItemDiscountAmount();
    }

    /**
     * Get the invoice total amount
     *
     * @return array<string, array<int<0, max>, array<string, mixed>>|float|int>
     */
    public function getItemTaxes()
    {
        $taxableAmount = $this->getTaxableBaseAmount();
        $totalTaxes = 0;
        $calculatedTaxes = [
            'details' => [],
            'total' => 0,
        ];
        if ($this->taxes == null) {
            $this->taxes = [];
        }
        foreach ($this->taxGroups as $tax) {
        }
        $calculatedTaxes['total'] = $totalTaxes;

        return $calculatedTaxes;
    }

    /**
     * Get the order total amount
     *
     * @return float
     */
    public function getTaxesRate()
    {
        $taxableAmount = $this->getTaxableBaseAmount();
        $totalTaxesRate = 0;
        foreach ($this->taxGroups as $tax) {
        }

        return $totalTaxesRate;
    }

    /**
     * Get the invoice total amount
     */
    public function getItemDiscountAmount(): float
    {
        return $this->getItemSubTotalAmount() * ($this->discount / 100);
    }
}
