<?php

namespace App\Support\Traits;

use App\Models\TaxGroup;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTaxGroups
{
    public function taxGroups(): MorphToMany
    {
        return $this->morphToMany(
            TaxGroup::class,
            'model',
            'model_tax_groups',
            'model_id',
            'tax_group_id'
        );
    }
}
