<?php

namespace App\Rules;

use App\Models\SupplierProduct as ModelsSupplierProduct;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class SupplierProduct implements DataAwareRule, Rule
{
    private $data = null;

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
        $supplierId = $this->data['supplier']['id'];
        $productId = $this->data['product']['id'];

        if ($this->data['id']) {
            $count = ModelsSupplierProduct::where('supplier_id', $supplierId)
                ->where('product_id', $productId)
                ->whereNot('id', $this->data['id'])
                ->count();
        } else {
            $count = ModelsSupplierProduct::where('supplier_id', $supplierId)
                ->where('product_id', $productId)
                ->count();
        }

        if ($count > 0) {
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
        return 'Supplier already exist for this product';
    }
}
