<?php

namespace App\Rules;

use App\Models\PurchasesInvoice;
use App\Models\PurchasesOrder;
use App\Support\Interfaces\PurchasesInvoiceable;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class PurchasesInvoiceOrderItem implements DataAwareRule, Rule
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
        if (empty($data['purchasesInvoice'])) {
            return $this;
        }
        if (empty($data['purchasesInvoice']['id'])) {
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
        /** @var PurchasesInvoice|null */
        $invoice = PurchasesInvoice::find($this->data['purchasesInvoice']['id']);
        if (! $invoice) {
            $this->message = 'Invoice not found';

            return false;
        }
        if (! $invoice->purchasesOrder) {
            return true;
        }

        /** @var PurchasesOrder */
        $puchasesOrder = $invoice->getOrder();

        $invoicingState = $puchasesOrder->getInvoicingItemsState();
        $remainingItems = $invoicingState['remainingItems'];
        $resolver = json_api()->getDefaultResolver();

        /** @phpstan-ignore-next-line */
        $purchasesInvoiceable = $resolver->getType($this->data['purchasesInvoiceable']['type'])::find($this->data['purchasesInvoiceable']['id']);
        if (! $purchasesInvoiceable instanceof PurchasesInvoiceable) {
            $this->message = 'Item not invoiceable';

            return false;
        }

        if (! $remainingItems[$purchasesInvoiceable->getItemId()]) {
            $this->message = 'Item not found in order';

            return false;
        }

        $remainingItem = $remainingItems[$purchasesInvoiceable->getItemId()];
        if ($remainingItem['quantity'] < $this->data['quantity']) {
            $this->message = 'Quantity for item is greater than order remaining quantity';

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
