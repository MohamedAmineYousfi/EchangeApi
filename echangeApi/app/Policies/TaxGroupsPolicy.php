<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\TaxGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxGroupsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_TAXES);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaxGroup $category): bool
    {
        return $user->canAccessModelWithPermission($category, Permissions::PERM_VIEW_TAXES);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_TAXES);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaxGroup $category): bool
    {
        return $user->canAccessModelWithPermission($category, Permissions::PERM_EDIT_TAXES);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaxGroup $category): bool
    {
        return $user->canAccessModelWithPermission($category, Permissions::PERM_DELETE_TAXES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, TaxGroup $category)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, TaxGroup $category)
    {
        //
    }
}
