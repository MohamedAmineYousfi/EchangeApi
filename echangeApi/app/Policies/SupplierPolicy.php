<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_SUPPLIERS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return $user->canAccessModelWithPermission($supplier, Permissions::PERM_VIEW_SUPPLIERS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_SUPPLIERS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        return $user->canAccessModelWithPermission($supplier, Permissions::PERM_EDIT_SUPPLIERS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->canAccessModelWithPermission($supplier, Permissions::PERM_DELETE_SUPPLIERS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Supplier $supplier)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Supplier $supplier)
    {
        //
    }
}
