<?php

namespace App\Support\Interfaces;

use App\Models\Reseller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ?Reseller $reseller
 */
interface ResellerScopable extends Scopable
{
    /**
     * getReseller
     */
    public function getReseller(): ?Reseller;

    /**
     * Undocumented function
     */
    public function reseller(): BelongsTo;

    /**
     * Undocumented function
     */
    public function scopeReseller(Builder $query, string $reseller): Builder;
}
