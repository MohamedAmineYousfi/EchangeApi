<?php

namespace App\Support\Traits;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Contactable
{
    /**
     * A customer has many contacts
     */
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
}
