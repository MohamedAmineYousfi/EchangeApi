<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Package;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_PACKAGES);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_PACKAGES);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Package $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_PACKAGES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Package $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_PACKAGES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Package $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_PACKAGES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Package $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Package $model)
    {
        //
    }
}
