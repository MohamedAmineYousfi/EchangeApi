<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_USERS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_USERS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        if ($user->is($model)) {
            return true;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_USERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        if ($user->is($model)) {
            return true;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_USERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        if ($user->is($model)) {
            return false;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_USERS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
