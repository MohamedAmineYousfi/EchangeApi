<?php

namespace App\Support\Classes;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $code
 * @property float $amount
 * @property string $status
 */
abstract class BasePayment extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
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
    abstract public function handlePaymentCompleted();

    abstract public function refreshPayment();

    abstract public function getInvoice();

    abstract public function send();

    public const SOURCE_MANUAL = 'MANUAL';

    public const SOURCE_STRIPE = 'STRIPE';

    public const SOURCE_PAYPAL = 'PAYPAL';

    public const SOURCE_CASH = 'CASH';

    public const SOURCE_UNKNOWN = 'UNKNOWN';

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELED = 'CANCELED';

    protected $fillable = [
        'date',
        'code',
        'source',
        'status',
        'excerpt',
        'amount',
        'transaction_id',
        'transaction_data',
    ];

    protected $dates = [
        'date',
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

        static::creating(function (BasePayment $payment) {
            $payment->refreshPayment();
        });

        static::saved(function (BasePayment $payment) {
            $payment->allowedLocations()->sync($payment->getInvoice()->allowedLocations);
            $payment->handlePaymentCompleted();
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return [];
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function getObjectName(): string
    {
        return $this->code;
    }

    /**
     * @param  $name
     */
    public function scopeCode($query, $code): Builder
    {
        return $query->where('code', 'LIKE', "%$code%", 'or');
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeDateBetween($query, $dates): Builder
    {
        return $query->whereBetween('date', $dates);
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeCreatedAtBetween($query, $dates): Builder
    {
        return $query->whereBetween('payments.created_at', $dates);
    }
}
