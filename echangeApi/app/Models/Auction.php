<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $authorized_payments
 */
class Auction extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use HasFactory;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
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
        'organization_id',
        'name',
        'excerpt',
        'auction_type',
        'object_type',
        'start_at',
        'end_at',
        'activated_timer',
        'pre_opening_at',
        'listings_registrations_close_at',
        'listings_registrations_open_at',
        'extension_time',
        'delay',
        'authorized_payments',
        'country',
        'state',
        'city',
        'zipcode',
        'address',
        'lat',
        'long',
    ];

    protected $casts = [
        'pre_opening_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'listings_registrations_close_at' => 'datetime',
        'listings_registrations_open_at' => 'datetime',
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

        static::created(function ($auction) {
            BidStep::create([
                'amount_min' => 0,
                'amount_max' => 1000,
                'bid_amount' => 50,
                'auction_id' => $auction->id,
            ]);

            BidStep::create([
                'amount_min' => 1001,
                'amount_max' => 10000,
                'bid_amount' => 100,
                'auction_id' => $auction->id,
            ]);

            BidStep::create([
                'amount_min' => 10001,
                'amount_max' => null,
                'bid_amount' => 200,
                'auction_id' => $auction->id,
            ]);
        });
    }

    public function getObjectName(): string
    {
        return $this->name;
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

    protected function authorizedPayments(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'auction_managers');
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('auctions.name', 'LIKE', "%$search%")
                ->orWhere('auctions.excerpt', 'LIKE', "%$search%");
        })
            ->orWhereHas('managers', function ($subQuery) use ($search) {
                $subQuery->where('users.lastname', 'LIKE', "%$search%")
                    ->orWhere('users.firstname', 'LIKE', "%$search%");
            });
    }

    public function scopeStartAt($query, $startAt): Builder
    {
        return $query->where('start_at', '<=', $startAt);
    }

    public function scopeEndAt($query, $endAt): Builder
    {
        return $query->where('end_at', '>=', $endAt);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function auctionFees(): HasMany
    {
        return $this->hasMany(AuctionFee::class);
    }

    public function bidSteps(): HasMany
    {
        return $this->hasMany(BidStep::class);
    }
}
