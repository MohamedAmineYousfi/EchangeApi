<?php

namespace App\Rules;

use App\Models\WarehouseProduct;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class WarehouseProductUniqueProduct implements DataAwareRule, Rule
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
        if (! $this->data['id']) {
            $warehouseProduct = WarehouseProduct::where('warehouse_id', '=', $this->data['warehouse']['id'])
                ->where('product_id', '=', $this->data['product']['id'])
                ->first();
        } else {
            $warehouseProduct = WarehouseProduct::where('warehouse_id', '=', $this->data['warehouse']['id'])
                ->where('product_id', '=', $this->data['product']['id'])
                ->where('id', '!=', $this->data['id'])
                ->first();
        }
        if ($warehouseProduct) {
            $this->message = __('errors.product_x_already_added', [
                'product' => $warehouseProduct->product->name,
            ]);

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
