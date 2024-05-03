<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\PurchasesDelivery;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasesDeliveryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, PurchasesDelivery $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, PurchasesDelivery $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, PurchasesDelivery $model)
    {
        if ($model->status != PurchasesDelivery::STATUS_DRAFT) {
            return false;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, PurchasesDelivery $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, PurchasesDelivery $model)
    {
        //
    }
}
