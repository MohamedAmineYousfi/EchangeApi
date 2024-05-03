<?php

namespace App\Models\Scopes;

use App\Constants\Permissions;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Contracts\Resolver\ResolverInterface;
use CloudCreativity\LaravelJsonApi\Routing\Route;
use CloudCreativity\LaravelJsonApi\Services\JsonApiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PermissionsScope implements Scope
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

        if (! auth()->hasUser()) {
            return;
        }

        /** @var ?User */
        $user = auth()->user();
        if (! $user) {
            return;
        }

        if ($user->is_staff) {
            return;
        }

        if ($user->organization) {
            $builder->where('scope', '>=', Permissions::SCOPE_ORGANIZATION);
        } elseif ($user->reseller) {
            $builder->where('scope', '>=', Permissions::SCOPE_RESELLER);
        }
    }
}
