<?php

namespace App\Support\Traits;

use App\Models\NotificationSubscription;
use App\Models\User;
use App\Notifications\ObjectCreated;
use App\Notifications\ObjectDeleted;
use App\Notifications\ObjectUpdated;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Interfaces\ResellerScopable;
use CloudCreativity\LaravelJsonApi\Exceptions\RuntimeException;
use CloudCreativity\LaravelJsonApi\Routing\Route;
use Illuminate\Notifications\Notification as NotificationsNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

trait EventNotifiable
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function (EventNotifiableContract $model) {
            $user = auth()->user();
            if ($user) {
                /** @var Route */
                $currentRoute = app('json-api')->currentRoute();
                try {
                    foreach ($currentRoute->getTypes() as $type) {
                        if ($model instanceof $type) {
                            self::notifySubscribers($model, 'CREATE', new ObjectCreated($model, $user));
                            break;
                        }
                    }
                } catch (RuntimeException $e) {
                    Log::info($e);
                }
            }
        });

        static::updated(function (EventNotifiableContract $model) {
            $user = auth()->user();
            if ($user) {
                /** @var Route */
                $currentRoute = app('json-api')->currentRoute();
                try {
                    foreach ($currentRoute->getTypes() as $type) {
                        if ($model instanceof $type) {
                            self::notifySubscribers($model, 'UPDATE', new ObjectUpdated($model, $user));
                            break;
                        }
                    }
                } catch (RuntimeException $e) {
                    Log::info($e);
                }
            }
        });

        static::deleted(function (EventNotifiableContract $model) {
            $user = auth()->user();
            if ($user) {
                /** @var Route */
                $currentRoute = app('json-api')->currentRoute();
                try {
                    foreach ($currentRoute->getTypes() as $type) {
                        if ($model instanceof $type) {
                            self::notifySubscribers($model, 'DELETE', new ObjectDeleted($model, $user));
                            break;
                        }
                    }
                } catch (RuntimeException $e) {
                    Log::info($e);
                }
            }
        });
    }

    protected static function getEventSubscribers($model, string $event)
    {
        $currentRoute = app('json-api')->currentRoute();
        $modelSubscribers = NotificationSubscription::withoutGlobalScopes()
            ->where('event', $event)
            ->where('object_type', $currentRoute->getType())
            ->whereNull('object_id')
            ->get();
        $objectSubscribers = NotificationSubscription::withoutGlobalScopes()
            ->where('event', $event)
            ->where('object_type', $currentRoute->getType())
            ->where('object_id', $model->id)
            ->get();
        $subscribers = $modelSubscribers->merge($objectSubscribers)->unique('user_id');

        return $subscribers;
    }

    protected static function notifySubscribers(object $model, string $event, NotificationsNotification $notification)
    {
        /** @var ?User */
        $authUser = auth()->user();
        foreach (self::getEventSubscribers($model, $event) as $sub) {
            /** @var User */
            $user = User::withoutGlobalScopes()->find($sub->user_id);
            if ($authUser->isNot($user)) {
                if (! $user->is_staff) {
                    if ($model instanceof ResellerScopable) {
                        if ($user->reseller) {
                            if ($user->reseller->id == $model->getReseller()->id) {
                                Notification::send($user, $notification);
                            }
                        }
                    }
                    if ($model instanceof OrganizationScopable) {
                        if ($user->organization) {
                            if ($model->isLocationRestricted()) {
                                if ($user->restrict_to_locations) {
                                    $userAllowedLocations = collect(DB::select('
                                        SELECT location_id FROM model_allowed_locations 
                                        WHERE model_id = :model_id
                                        AND model_type = :model_type
                                        ', [
                                        'model_id' => $user->id,
                                        'model_type' => User::class,
                                    ]))->pluck('location_id');
                                    $modelAllowedLocations = $model->getAllowedLocations()->pluck('id');
                                    if ($userAllowedLocations->intersect($modelAllowedLocations)->count() > 0) {
                                        Notification::send($user, $notification);
                                    }
                                } else {
                                    Notification::send($user, $notification);
                                }
                            }
                        }
                    }
                } else {
                    Notification::send($user, $notification);
                }
            }
        }
    }
}
