<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\PurchasesInvoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasesInvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_PURCHASES_INVOICES);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_PURCHASES_INVOICES);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, PurchasesInvoice $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_VIEW_PURCHASES_INVOICES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, PurchasesInvoice $model)
    {
        return $user->canAccessModelWithPermission($model, Permissions::PERM_EDIT_PURCHASES_INVOICES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, PurchasesInvoice $model)
    {
        if ($model->status != PurchasesInvoice::STATUS_DRAFT) {
            return false;
        }

        return $user->canAccessModelWithPermission($model, Permissions::PERM_DELETE_PURCHASES_INVOICES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, PurchasesInvoice $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, PurchasesInvoice $model)
    {
        //
    }
}
