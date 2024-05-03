<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_FILES);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, File $file): bool
    {
        return $user->canAccessModelWithPermission($file, Permissions::PERM_VIEW_FILES);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_FILES);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, File $file): bool
    {
        return $user->canAccessModelWithPermission($file, Permissions::PERM_EDIT_FILES);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, File $file): bool
    {
        return $user->canAccessModelWithPermission($file, Permissions::PERM_DELETE_FILES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, File $file)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, File $file)
    {
        //
    }
}
