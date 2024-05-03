<?php

namespace App\Support\Interfaces;

interface Taxable
{
    /** @return array<TaxableItem> */
    public function getTaxableItems(): array;
}
