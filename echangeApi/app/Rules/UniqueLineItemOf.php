<?php

namespace App\Rules;

use App\Support\Classes\BaseDelivery;
use App\Support\Classes\BaseInvoice;
use App\Support\Classes\BaseOrder;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class UniqueLineItemOf implements DataAwareRule, Rule
{
    private $data = null;

    private $message;

    /**
     * @var class-string<BaseInvoice|BaseOrder|BaseDelivery>
     */
    private $parentClass;

    /**
     * @var string
     */
    private $parentProperty;

    /**
     * @var string
     */
    private $childProperty;

    public function __construct($parentClass, $parentProperty, $childProperty)
    {
        $this->parentClass = $parentClass;
        $this->parentProperty = $parentProperty;
        $this->childProperty = $childProperty;
    }

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
        $childProperty = $this->childProperty;
        if (empty($this->data[$childProperty]['id'])) {
            $this->message = 'the item property "'.$childProperty.'" does not exist in payload';

            return false;
        }

        $childId = $this->data[$childProperty]['id'];

        /** @var BaseInvoice|BaseOrder|BaseDelivery|null */
        $parent = $this->parentClass::find($this->data[$this->parentProperty]['id']);
        if (! $parent) {
            $this->message = 'the parent does not exist with class"'.$this->parentProperty.'"';

            return false;
        }

        foreach ($parent->items as $line) {
            $item = $line->$childProperty;
            if ($this->data['id'] == $line->id) {
                if ($item->id == $childId) {
                    continue;
                }
            }
            if ($item->id == $childId) {
                $this->message = 'A line with this item already exists';

                return false;
            }
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
