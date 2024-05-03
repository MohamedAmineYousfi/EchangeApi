<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\SupplierProduct;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_PRODUCTS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_PRODUCTS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, SupplierProduct $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_PRODUCTS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, SupplierProduct $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_PRODUCTS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, SupplierProduct $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_PRODUCTS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, SupplierProduct $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, SupplierProduct $model)
    {
        //
    }
}
