<?php

namespace App\Models\Scopes;

use App\Models\Organization;
use App\Models\Reseller;
use App\Models\Role;
use App\Models\User;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Interfaces\ResellerScopable;
use CloudCreativity\LaravelJsonApi\Contracts\Resolver\ResolverInterface;
use CloudCreativity\LaravelJsonApi\Routing\Route;
use CloudCreativity\LaravelJsonApi\Services\JsonApiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        /** @var ResolverInterface */
        $resolver = json_api()->getDefaultResolver();
        /** @var Route $route */
        $route = app(JsonApiService::class)->currentRoute();
        $requestedType = $resolver->getType($route->getResourceType());

        if (! $requestedType) {
            return;
        }

        if (! $model instanceof $requestedType) {
            return;
        }

        if (! $model instanceof OrganizationScopable) {
            return;
        }

        if (! auth()->hasUser()) {
            return;
        }

        /** @var ?User */
        $user = auth()->user();
        if (! $user) {
            return;
        }

        if ($user->organization) {
            /** @var Organization */
            $organization = $user->organization;
            $builder->where($model->getTable().'.organization_id', '=', $organization->id);
        }

        if (! $model instanceof ResellerScopable) {
            if ($user->reseller && ! $user->organization) {
                /** @var Reseller */
                $reseller = $user->reseller;
                $builder->whereHas(
                    'organizationReseller',
                    function (Builder $query) use ($reseller) {
                        $query->where('resellers.id', '=', $reseller->id);
                    }
                );
            }
        }

        if (json_api()->isByResource()) {
            /** @var Route */
            $currentRoute = app(JsonApiService::class)->currentRoute();
            $ressourcetype = $currentRoute->getResourceType();
            if (json_api()->getDefaultResolver()->getType($ressourcetype) == User::class) {
                if ($currentRoute->getResourceId() == $user->id) {
                    return;
                }
            }
        }

        if ($model->isLocationRestricted()) {
            if ($user->restrict_to_locations) {
                if (! $model instanceof Role) {
                    $builder->whereHas('allowedLocations', function (Builder $subQuery) use ($user) {
                        $subQuery->whereIn('locations.id', $user->getAllowedLocationsRaw());
                    });
                }
            }
        }
    }
}
