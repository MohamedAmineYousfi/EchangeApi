<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Import;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_IMPORTS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Import $importation): bool
    {
        return $user->canAccessModelWithPermission($importation, Permissions::PERM_VIEW_IMPORTS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_IMPORTS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Import $importation): bool
    {
        return $user->canAccessModelWithPermission($importation, Permissions::PERM_EDIT_IMPORTS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Import $importation): bool
    {
        return $user->canAccessModelWithPermission($importation, Permissions::PERM_DELETE_IMPORTS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Import $importation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Import $importation)
    {
        //
    }
}
