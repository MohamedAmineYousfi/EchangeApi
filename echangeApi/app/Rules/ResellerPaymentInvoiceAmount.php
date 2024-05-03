<?php

namespace App\Rules;

use App\Models\ResellerInvoice;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class ResellerPaymentInvoiceAmount implements DataAwareRule, Rule
{
    private $data = null;

    /** @var ResellerInvoice|null */
    private $invoice = null;

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        if (empty($this->data['invoice'])) {
            return $this;
        }
        if (empty($this->data['invoice']['id'])) {
            return $this;
        }

        $this->invoice = ResellerInvoice::find($this->data['invoice']['id']);

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
        if (empty($this->invoice)) {
            return false;
        }
        if (bccomp(strval($this->invoice->getInvoiceTotalPaied() + $value), strval($this->invoice->getInvoiceTotalAmount())) == 1) {
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
        return 'Payment amount is greater than invoice amount';
    }
}
