<?php

namespace App\Support\Classes;

use App\Models\User;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $code
 */
abstract class BaseDelivery extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use SoftDeletes;

    /************ Abstract functions ************/
    abstract public function refreshDelivery();

    abstract public function getOrder();

    abstract public function items();

    abstract public function send();

    /************ Common functions, attributes and constants ************/
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_VALIDATED = 'VALIDATED';

    public const STATUS_CANCELED = 'CANCELED';

    protected $fillable = [
        'code',
        'expiration_time',
        'excerpt',
        'status',
    ];

    protected $dates = [
        'expiration_time',
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
        self::organizationScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();

        static::saving(function (BaseDelivery $model) {
            $model->refreshDelivery();
        });
        self::saved(function (BaseDelivery $baseDelivery) {
            if ($baseDelivery->getOrder()) {
                $baseDelivery->getOrder()->setOrderStatus();
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return [];
    }

    public function getObjectName(): string
    {
        return $this->code;
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * An Delivery is created by a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /************ scopes  ************/
    /**
     * @param  $name
     */
    public function scopeCode($query, $code): Builder
    {
        return $query->where('code', 'LIKE', "%$code%", 'and');
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeCreatedAtBetween($query, $dates): Builder
    {
        return $query->whereBetween('created_at', $dates);
    }
}
