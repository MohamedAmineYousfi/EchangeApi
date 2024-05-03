<?php

namespace App\Rules;

use App\Models\SalesDelivery;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class SalesDeliveryOrderItem implements DataAwareRule, Rule
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
        if (empty($data['salesDelivery'])) {
            return $this;
        }
        if (empty($data['salesDelivery']['id'])) {
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
        /** @var SalesDelivery|null */
        $delivery = SalesDelivery::find($this->data['salesDelivery']['id']);
        if (! $delivery) {
            $this->message = 'Delivery not found';

            return false;
        }
        if (! $delivery->salesOrder) {
            return true;
        }

        $orderItemCount = 0;
        foreach ($delivery->salesOrder->items as $item) {
            if ($item->salesOrderable) {
                if ($item->salesOrderable->id == $this->data['salesDeliverable']['id']) {
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
            if ($item->salesDeliverable) {
                if ($item->salesDeliverable->id == $this->data['salesDeliverable']['id']) {
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
