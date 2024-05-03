<?php

namespace App\Support\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface TaxableItem
{
    public function taxGroups(): MorphToMany;
}
