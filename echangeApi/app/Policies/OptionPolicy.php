<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Option;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_OPTIONS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Option $option): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_OPTIONS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Option $option): bool
    {
        return $user->canAccessModelWithPermission($option, Permissions::PERM_EDIT_OPTIONS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Option $option): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Option $option)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Option $option)
    {
        //
    }
}
