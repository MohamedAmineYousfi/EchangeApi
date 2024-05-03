<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_PROPERTIES);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property): bool
    {
        return $user->canAccessModelWithPermission($property, Permissions::PERM_VIEW_PROPERTIES);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_PROPERTIES);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->canAccessModelWithPermission($property, Permissions::PERM_EDIT_PROPERTIES);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->canAccessModelWithPermission($property, Permissions::PERM_DELETE_PROPERTIES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Property $property)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Property $property)
    {
        //
    }
}
