<?php

namespace App\Rules;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class SalesInvoiceOrderItem implements DataAwareRule, Rule
{
    private $data = null;

    private $message;

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        if (empty($data['salesInvoice'])) {
            return $this;
        }
        if (empty($data['salesInvoice']['id'])) {
            return $this;
        }

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var SalesInvoice|null */
        $invoice = SalesInvoice::find($this->data['salesInvoice']['id']);
        if (! $invoice) {
            $this->message = 'Invoice not found';

            return false;
        }
        if (! $invoice->salesOrder) {
            return true;
        }

        $orderItemCount = 0;
        foreach ($invoice->salesOrder->items as $item) {
            if ($item->salesOrderable) {
                if ($item->salesOrderable->id == $this->data['salesInvoiceable']['id']) {
                    $orderItemCount += $item->quantity;
                }
            }
        }
        if ($orderItemCount == 0) {
            $this->message = 'Item not found in order';

            return false;
        }

        $invoiceItemCount = 0;
        /** @var SalesInvoiceItem $item */
        foreach ($invoice->items as $item) {
            if ($item->salesInvoiceable) {
                if ($item->salesInvoiceable->id == $this->data['salesInvoiceable']['id']) {
                    if ($this->data['id'] != $item->id) {
                        $invoiceItemCount += $item->quantity;
                    }
                }
            }
        }

        if (($invoiceItemCount + $value) > $orderItemCount) {
            $this->message = 'Total Invoice quantity for product is greater than order quantity';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return $this->message;
    }
}
