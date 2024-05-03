<?php

namespace App\Rules;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Validation\Rule;

class StringTimeInterval implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @phpstan-ignore-next-line */
        if (CarbonInterval::createFromDateString($value)) {
            return true;
        }

        /** @phpstan-ignore-next-line */
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return 'Invalid time interval';
    }
}
