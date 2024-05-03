<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\ResellerService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResellerServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_RESELLER_SERVICES);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_RESELLER_SERVICES);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, ResellerService $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_RESELLER_SERVICES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, ResellerService $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_RESELLER_SERVICES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, ResellerService $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_RESELLER_SERVICES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, ResellerService $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, ResellerService $model)
    {
        //
    }
}
