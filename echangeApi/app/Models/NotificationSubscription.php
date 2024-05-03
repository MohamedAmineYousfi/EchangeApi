<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'object_type',
        'object_id',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('notification-subscription-user', function (Builder $builder) {
            /** @var ?User */
            $user = auth()->user();
            if ($user) {
                $builder->where('user_id', '=', $user->id);
            }
        });

        self::creating(function (NotificationSubscription $sub) {
            $user = auth()->user();
            if ($user) {
                $sub->object_type = json_api()->getDefaultResolver()->getType($sub->object_type);
                $sub->user()->associate($user);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder  $query
     */
    public function scopeObjectType($query, $objectType): Builder
    {
        $type = json_api()->getDefaultResolver()->getType($objectType);

        return $query->where('notification_subscriptions.object_type', $type);
    }
}
