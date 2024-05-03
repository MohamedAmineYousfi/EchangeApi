<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\ResellerInvoiceItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResellerInvoiceItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permissions::PERM_CREATE_RESELLER_INVOICES);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::PERM_VIEW_ANY_RESELLER_INVOICES);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, ResellerInvoiceItem $model)
    {
        return $user->canAccessModelWithPermission($model->resellerInvoice, Permissions::PERM_VIEW_RESELLER_INVOICES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, ResellerInvoiceItem $model)
    {
        return $user->canAccessModelWithPermission($model->resellerInvoice, Permissions::PERM_VIEW_RESELLER_INVOICES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, ResellerInvoiceItem $model)
    {
        return $user->canAccessModelWithPermission($model->resellerInvoice, Permissions::PERM_DELETE_RESELLER_INVOICES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, ResellerInvoiceItem $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, ResellerInvoiceItem $model)
    {
        //
    }
}
