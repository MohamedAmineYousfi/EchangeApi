<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\PurchasesOrderItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasesOrderItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_PURCHASES_ORDERS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_PURCHASES_ORDERS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, PurchasesOrderItem $model)
    {
        return $user->canAccessModelWithPermission($model->purchasesorder, Permissions::PERM_VIEW_PURCHASES_ORDERS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, PurchasesOrderItem $model)
    {
        return $user->canAccessModelWithPermission($model->purchasesorder, Permissions::PERM_EDIT_PURCHASES_ORDERS);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, PurchasesOrderItem $model)
    {
        return $user->canAccessModelWithPermission($model->purchasesorder, Permissions::PERM_DELETE_PURCHASES_ORDERS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, PurchasesOrderItem $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, PurchasesOrderItem $model)
    {
        //
    }
}
