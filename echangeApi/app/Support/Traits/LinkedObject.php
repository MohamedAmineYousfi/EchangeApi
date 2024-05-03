<?php

namespace App\Support\Traits;

use App\Models\Import;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait LinkedObject
{
    /**
     * A model (supplier) has many imports
     */
    public function imports(): MorphMany
    {
        return $this->morphMany(Import::class, 'linked_object');
    }
}
