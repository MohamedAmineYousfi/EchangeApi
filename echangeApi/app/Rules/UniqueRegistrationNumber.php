<?php

namespace App\Rules;

use App\Models\Property;
use Illuminate\Contracts\Validation\Rule;

class UniqueRegistrationNumber implements Rule
{
    protected $propertyId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($propertyId)
    {
        $this->propertyId = $propertyId;
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
        // Votre logique pour vérifier l'unicité ici
        return ! Property::where('registration_number', $value)
            ->where('id', '<>', $this->propertyId)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.unique', ['attribute' => __('registration_number')]);
    }
}
