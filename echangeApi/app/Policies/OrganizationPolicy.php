<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_ORGANIZATIONS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_ORGANIZATIONS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Organization $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_ORGANIZATIONS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return bool
     */
    public function update(User $user, Organization $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_ORGANIZATIONS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return bool
     */
    public function delete(User $user, Organization $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_ORGANIZATIONS);
    }
}
