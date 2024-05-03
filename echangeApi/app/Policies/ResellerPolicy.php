<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Reseller;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResellerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_RESELLERS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_RESELLERS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Reseller $model)
    {
        return $user->can(Permissions::PERM_VIEW_RESELLERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return bool
     */
    public function update(User $user, Reseller $model)
    {
        return $user->can(Permissions::PERM_EDIT_RESELLERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return bool
     */
    public function delete(User $user, Reseller $model)
    {
        return $user->can(Permissions::PERM_DELETE_RESELLERS);
    }
}
