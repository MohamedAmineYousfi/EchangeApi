<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the warehouse can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_WAREHOUSES);
    }

    /**
     * Determine whether the warehouse can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_WAREHOUSES);
    }

    /**
     * Determine whether the warehouse can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Warehouse $model)
    {
        if ($user->is($model)) {
            return true;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_WAREHOUSES);
    }

    /**
     * Determine whether the warehouse can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Warehouse $model)
    {
        if ($user->is($model)) {
            return true;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_WAREHOUSES);
    }

    /**
     * Determine whether the warehouse can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Warehouse $model)
    {
        if ($user->is($model)) {
            return false;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_WAREHOUSES);
    }

    /**
     * Determine whether the warehouse can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Warehouse $model)
    {
        //
    }

    /**
     * Determine whether the warehouse can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Warehouse $model)
    {
        //
    }
}
