<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuctionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PERM_VIEW_ANY_AUCTIONS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Auction $auction): bool
    {
        return $user->canAccessModelWithPermission($auction, Permissions::PERM_VIEW_AUCTIONS);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::PERM_CREATE_AUCTIONS);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Auction $auction): bool
    {
        return $user->canAccessModelWithPermission($auction, Permissions::PERM_EDIT_AUCTIONS);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Auction $auction): bool
    {
        return $user->canAccessModelWithPermission($auction, Permissions::PERM_DELETE_AUCTIONS);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, Auction $auction)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, Auction $auction)
    {
        //
    }
}
