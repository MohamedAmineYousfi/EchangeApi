<?php

namespace App\Rules;

use App\Models\PurchasesDelivery;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class PurchasesDeliveryOrderItem implements DataAwareRule, Rule
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
        if (empty($data['purchasesDelivery'])) {
            return $this;
        }
        if (empty($data['purchasesDelivery']['id'])) {
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
        /** @var PurchasesDelivery|null */
        $delivery = PurchasesDelivery::find($this->data['purchasesDelivery']['id']);
        if (! $delivery) {
            $this->message = 'Delivery not found';

            return false;
        }
        if (! $delivery->purchasesOrder) {
            return true;
        }

        $orderItemCount = 0;
        foreach ($delivery->purchasesOrder->items as $item) {
            if ($item->purchasesOrderable) {
                if ($item->purchasesOrderable->id == $this->data['purchasesDeliverable']['id']) {
                    $orderItemCount += $item->quantity;
                }
            }
        }
        if ($orderItemCount == 0) {
            $this->message = 'Item not found in order';

            return false;
        }

        $deliveryItemCount = 0;
        foreach ($delivery->items as $item) {
            if ($item->purchasesDeliverable) {
                if ($item->purchasesDeliverable->id == $this->data['purchasesDeliverable']['id']) {
                    if ($this->data['id'] != $item->id) {
                        $deliveryItemCount += $item->quantity;
                    }
                }
            }
        }

        if (($deliveryItemCount + $value) > $orderItemCount) {
            $this->message = 'Total Delivery quantity for product is greater than order quantity';

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
