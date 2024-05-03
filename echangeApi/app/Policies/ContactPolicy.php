<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_CONTACTS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contact $contact): bool
    {
        return $user->canAccessModelWithPermission($contact, Permissions::PERM_VIEW_CONTACTS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_CONTACTS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contact $contact): bool
    {
        return $user->canAccessModelWithPermission($contact, Permissions::PERM_EDIT_CONTACTS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contact $contact): bool
    {
        return $user->canAccessModelWithPermission($contact, Permissions::PERM_DELETE_CONTACTS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Contact $contact)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Contact $contact)
    {
        //
    }
}
