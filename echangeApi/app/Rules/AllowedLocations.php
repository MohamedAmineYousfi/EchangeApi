<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class AllowedLocations implements DataAwareRule, Rule
{
    private $data = null;

    private $message = null;

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
        /** @var ?User */
        $user = auth()->user();
        if ($user->restrict_to_locations) {
            $userAllowedLocations = $user->allowedLocations->pluck('id');
            if (count($this->data['allowedLocations']) == 0) {
                $this->message = '"allowedLocations" field is required.';

                return false;
            }
            foreach ($this->data['allowedLocations'] as $loc) {
                if (! $userAllowedLocations->contains($loc['id'])) {
                    $this->message = 'User not allowed to access the location "'.$loc['id'].'".';

                    return false;
                }
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
