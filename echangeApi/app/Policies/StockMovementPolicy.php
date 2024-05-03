<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockMovementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_STOCK_MOVEMENTS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_STOCK_MOVEMENTS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, StockMovement $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_STOCK_MOVEMENTS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, StockMovement $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_STOCK_MOVEMENTS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, StockMovement $model)
    {
        if ($model->status != StockMovement::STATUS_DRAFT) {
            return false;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_STOCK_MOVEMENTS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, StockMovement $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, StockMovement $model)
    {
        //
    }
}
