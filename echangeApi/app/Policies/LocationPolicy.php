<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_LOCATIONS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Location $location): bool
    {
        return $user->canAccessModelWithPermission($location, Permissions::PERM_VIEW_LOCATIONS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_LOCATIONS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Location $location): bool
    {
        return $user->canAccessModelWithPermission($location, Permissions::PERM_EDIT_LOCATIONS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Location $location): bool
    {
        return $user->canAccessModelWithPermission($location, Permissions::PERM_DELETE_LOCATIONS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Location $location)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Location $location)
    {
        //
    }
}
