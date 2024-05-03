<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\PurchasesDeliveryItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasesDeliveryItemPolicy
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
    public function view(User $user, PurchasesDeliveryItem $model)
    {
        return $user->canAccessModelWithPermission($model->purchasesDelivery, Permissions::PERM_VIEW_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, PurchasesDeliveryItem $model)
    {
        return $user->canAccessModelWithPermission($model->purchasesDelivery, Permissions::PERM_EDIT_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, PurchasesDeliveryItem $model)
    {
        return $user->canAccessModelWithPermission($model->purchasesDelivery, Permissions::PERM_DELETE_PURCHASES_DELIVERIES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, PurchasesDeliveryItem $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, PurchasesDeliveryItem $model)
    {
        //
    }
}
