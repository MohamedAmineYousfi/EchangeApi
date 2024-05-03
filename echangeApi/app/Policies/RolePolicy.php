<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_ROLES);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_ROLES);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Role $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_ROLES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return bool
     */
    public function update(User $user, Role $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return bool
     */
    public function delete(User $user, Role $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_ROLES);
    }
}
