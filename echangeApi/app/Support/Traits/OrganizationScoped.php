<?php

namespace App\Support\Traits;

use App\Models\Location;
use App\Models\Organization;
use App\Models\Reseller;
use App\Models\Scopes\OrganizationScope;
use App\Models\User;
use App\Support\Interfaces\OrganizationScopable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait OrganizationScoped
{
    public function isLocationRestricted(): bool
    {
        return true;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new OrganizationScope());

        static::saving(function (OrganizationScopable $model) {
            /** @var ?User */
            $user = auth()->user();
            if ($user) {
                if ($user->getOrganization()) {
                    if (! $model->getOrganization()) {
                        $model->organization()->associate($user->organization);
                    } else {
                        if (! $model->getOrganization()->is($user->organization)) {
                            abort(403, 'You are not allowed to edit this model');
                        }
                    }
                }
            }
        });
    }

    /**
     * Undocumented function
     *
     * @return ?Organization
     */
    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    /**
     * Undocumented function
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Undocumented function
     */
    public function organizationReseller(): HasOneThrough
    {
        return $this->hasOneThrough(
            Reseller::class,
            Organization::class,
            'id',
            'id',
            'organization_id',
            'reseller_id'
        );
    }

    /**
     * Undocumented function
     */
    public function getAllowedLocations(): Collection
    {
        return $this->allowedLocations;
    }

    /**
     * Get all of the allowedLocations for the post.
     */
    public function allowedLocations(): MorphToMany
    {
        return $this->morphToMany(
            Location::class,
            'model',
            'model_allowed_locations',
            'model_id',
            'location_id'
        );
    }

    public function scopeOrganization(Builder $query, ?string $organization): Builder
    {
        if ($organization) {
            return $query->where($query->getModel()->getTable().'.organization_id', '=', $organization, 'and');
        } else {
            return $query->whereNull($query->getModel()->getTable().'.organization_id');
        }
    }

    public function scopeAllowedLocations(Builder $query, array $allowedLocations): Builder
    {
        return $query->whereHas('allowedLocations', function (Builder $subQuery) use ($allowedLocations) {
            $subQuery->whereIn('locations.id', $allowedLocations);
        });
    }
}
