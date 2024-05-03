<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Organization;
use App\Models\PurchasesPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasesPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_PURCHASES_PAYMENTS);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_PURCHASES_PAYMENTS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, PurchasesPayment $model)
    {
        if ($user->reseller && ! $user->organization) {
            if ($model->invoice->issuer instanceof Organization) {
                if ($user->reseller->id == $model->invoice->issuer->reseller->id) {
                    return $user->can(Permissions::PERM_VIEW_PURCHASES_INVOICES);
                }
            }
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_ANY_PURCHASES_PAYMENTS);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, PurchasesPayment $model)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, PurchasesPayment $model)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, PurchasesPayment $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, PurchasesPayment $model)
    {
        //
    }
}
