<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Subscription extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_time',
        'end_time',
    ];

    /**
     * dates
     *
     * @var array<int, string>
     */
    protected $dates = [
        'start_time',
        'end_time',
    ];

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

        static::saving(function (Subscription $subscription) {
            if (! $subscription->code) {
                $invoicesCount = Subscription::withoutGlobalScopes()->count() + 1;
                $subscription->code = 'SUB-'.
                    Carbon::now()->format('Ymd').
                    str_pad(strval($invoicesCount), 3, '0', STR_PAD_LEFT);
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

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function getObjectName(): string
    {
        return $this->organization->name.' -> '.$this->package->name;
    }

    /**
     * package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * organization
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @param  $name
     */
    public function scopeCode($query, $code): Builder
    {
        return $query->where('subscriptions.code', 'LIKE', "%$code%", 'and');
    }

    /**
     * @param  $name
     */
    public function scopePackage($query, $package): Builder
    {
        return $query->where('subscriptions.package_id', '=', $package, 'and');
    }

    /**
     * Scope the query for start_time between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeStartTimeBetween($query, $dates): Builder
    {
        return $query->whereBetween('subscriptions.start_time', $dates);
    }

    /**
     * Scope the query for end_time between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeEndTimeBetween($query, $dates): Builder
    {
        return $query->whereBetween('subscriptions.end_time', $dates);
    }
}
