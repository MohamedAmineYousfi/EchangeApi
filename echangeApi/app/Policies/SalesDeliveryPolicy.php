<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\SalesDelivery;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesDeliveryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_SALES_DELIVERIES);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_SALES_DELIVERIES);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, SalesDelivery $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_SALES_DELIVERIES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, SalesDelivery $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_SALES_DELIVERIES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, SalesDelivery $model)
    {
        if ($model->status != SalesDelivery::STATUS_DRAFT) {
            return false;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_SALES_DELIVERIES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, SalesDelivery $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, SalesDelivery $model)
    {
        //
    }
}
