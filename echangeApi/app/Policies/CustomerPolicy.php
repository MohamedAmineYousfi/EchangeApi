<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_CUSTOMERS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_CUSTOMERS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Customer $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_CUSTOMERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Customer $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_CUSTOMERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Customer $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_CUSTOMERS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Customer $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Customer $model)
    {
        //
    }
}
