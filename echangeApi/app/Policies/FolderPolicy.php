<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_FOLDERS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Folder $folder): bool
    {
        return $user->canAccessModelWithPermission($folder, Permissions::PERM_VIEW_FOLDERS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_FOLDERS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Folder $folder): bool
    {
        return $user->canAccessModelWithPermission($folder, Permissions::PERM_EDIT_FOLDERS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Folder $folder): bool
    {
        return $user->canAccessModelWithPermission($folder, Permissions::PERM_DELETE_FOLDERS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Folder $folder)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Folder $folder)
    {
        //
    }
}
