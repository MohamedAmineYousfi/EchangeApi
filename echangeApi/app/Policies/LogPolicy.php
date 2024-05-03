<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Log;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_LOGS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Log $model)
    {
        return $user->can(Permissions::PERM_VIEW_LOGS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Log $model)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Log $model)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Log $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Log $model)
    {
        //
    }
}
