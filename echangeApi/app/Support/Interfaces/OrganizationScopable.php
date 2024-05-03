<?php

namespace App\Support\Interfaces;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property mixed $allowedLocations
 * @property ?Organization $organization
 */
interface OrganizationScopable extends Scopable
{
    /**
     * set this value to false if must be
     * organizationscoped but does not
     * restrict to locations
     **/
    public function isLocationRestricted(): bool;

    /**
     * getOrganization
     *
     * @return ?Organization
     */
    public function getOrganization(): ?Organization;

    /**
     * Undocumented function
     */
    public function organization(): BelongsTo;

    /**
     * Undocumented function
     */
    public function organizationReseller(): HasOneThrough;

    /**
     * Undocumented function
     */
    public function getAllowedLocations(): Collection;

    public function allowedLocations(): MorphToMany;

    /**
     * Undocumented function
     */
    public function scopeOrganization(Builder $query, string $organization): Builder;

    /**
     * Undocumented function
     */
    public function scopeAllowedLocations(Builder $query, array $allowedLocations): Builder;
}
