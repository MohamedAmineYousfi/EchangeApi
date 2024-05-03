<?php

namespace App\Models\Scopes;

use App\Models\User;
use CloudCreativity\LaravelJsonApi\Contracts\Resolver\ResolverInterface;
use CloudCreativity\LaravelJsonApi\Routing\Route;
use CloudCreativity\LaravelJsonApi\Services\JsonApiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class DocumentScope implements Scope
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

        $builder->where(function (Builder $builder) use ($user, $model) {

            if ($user->organization) {
                $builder->where($model->getTable().'.organization_id', '=', $user->organization->id);
            }

            if ($user->restrict_to_locations) {
                $builder->where(function (Builder $subBuilder) use ($user) {
                    $allowedLocations = DB::select('
                    SELECT location_id FROM model_allowed_locations 
                    WHERE model_id = :model_id
                        AND model_type = :model_type
                ', [
                        'model_id' => $user->id,
                        'model_type' => User::class,
                    ]);
                    $allowedLocations = array_map(function ($item) {
                        return $item->location_id;
                    }, $allowedLocations);

                    $subBuilder->whereHas('allowedLocations', function (Builder $subQuery) use ($allowedLocations) {
                        $subQuery->whereIn('locations.id', $allowedLocations);
                    });
                    $subBuilder->orWhereDoesntHave('allowedLocations');
                });
            }

            $builder->where(function (Builder $subBuilder) use ($user) {
                $usersRoles = $user->roles()->pluck('id');
                $subBuilder->whereHas('roles', function ($query) use ($usersRoles) {
                    $query->whereIn('roles.id', $usersRoles);
                });
                $subBuilder->orWhereDoesntHave('roles');
            });

            $builder->where(function (Builder $subBuilder) use ($user) {
                $subBuilder->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', '=', $user->id);
                });
                $subBuilder->orWhereDoesntHave('users');
            });
        });

        $builder->orWhere('owner_id', '=', $user->id);
    }
}
