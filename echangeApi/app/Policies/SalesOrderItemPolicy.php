<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\SalesOrderItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesOrderItemPolicy
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
    public function view(User $user, SalesOrderItem $model)
    {
        return $user->canAccessModelWithPermission($model->salesorder, Permissions::PERM_VIEW_SALES_ORDERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, SalesOrderItem $model)
    {
        return $user->canAccessModelWithPermission($model->salesorder, Permissions::PERM_EDIT_SALES_ORDERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, SalesOrderItem $model)
    {
        return $user->canAccessModelWithPermission($model->salesorder, Permissions::PERM_DELETE_SALES_ORDERS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, SalesOrderItem $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, SalesOrderItem $model)
    {
        //
    }
}
