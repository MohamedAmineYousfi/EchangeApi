<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_SALES_ORDERS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_SALES_ORDERS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, SalesOrder $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_SALES_ORDERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, SalesOrder $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_SALES_ORDERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, SalesOrder $model)
    {
        if ($model->status != SalesOrder::STATUS_DRAFT) {
            return false;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_SALES_ORDERS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, SalesOrder $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, SalesOrder $model)
    {
        //
    }
}
