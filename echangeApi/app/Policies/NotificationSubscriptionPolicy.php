<?php

namespace App\Policies;

use App\Models\NotificationSubscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationSubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NotificationSubscription $notificationSubscription): bool
    {
        return $user->is($notificationSubscription->user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NotificationSubscription $notificationSubscription): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NotificationSubscription $notificationSubscription): bool
    {
        return $user->is($notificationSubscription->user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return void
     */
    public function restore(User $user, NotificationSubscription $notificationSubscription)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return void
     */
    public function forceDelete(User $user, NotificationSubscription $notificationSubscription)
    {
        //
    }
}
