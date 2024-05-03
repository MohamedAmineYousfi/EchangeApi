<?php

namespace App\Models;

use App\Constants\Permissions;
use App\Constants\PropertyInformation;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property transactions
 */
class Property extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
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
        'owed_taxes_school_board',
        'owed_taxes_municipality',
        'organization_id',
        'auction_id',
        'created_by',
        'updated_by',
        'status',
        'sold_at',
        'mrc_fees',
        'sold_amount',
        'bid_starting_amount',
        'excerpt',
        'taxable',
        'registration_number',
        'batch_numbers',
        'property_type',
        'country',
        'state',
        'city',
        'zipcode',
        'address',
        'designation',
        'active',
        'batches',
        'property_number',
        'acquisition_method',
        'acquisition_number',
        'sale_confirmed_at',
        'approved_at',
        'cancel_reason',
        'customer',
        'transactions',
        'transaction_date',
        'payment_received_by',
        'transaction_excerpt',
    ];

    protected $appends = [];

    protected $casts = [
        'sale_confirmed_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'sold_at' => 'date',
        'transaction_date' => 'date',
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

        static::creating(function ($property) {
            $property->active = true;
            $property->created_by = Auth::id();
            $property->updated_by = Auth::id();

            if ($property->isDirty(['status']) && $property->status === PropertyInformation::STATUS_CONFIRMED && $property->sale_confirmed_at === null) {
                $property->sale_confirmed_at = now();
            }

            if ($property->isDirty(['status']) && !Auth::user()->can(Permissions::PERM_CHANGE_APPROVED_STATUS_PROPERTIES) && $property->sale_confirmed_at != null) {
                abort(403, __('notifications.unauthorized_to_change_approved_status_for_properties', []));
            }

            if ($property->isDirty(['status']) && $property->status === PropertyInformation::STATUS_APPROVED) {
                $property->approved_at = now();
            } else {
                $property->approved_at = null;
            }

            if (!$property->auction_id) {
                $auction = Auction::where('organization_id', $property->organization->id)
                    ->where('listings_registrations_open_at', '<=', Carbon::now())
                    ->where('listings_registrations_close_at', '>=', Carbon::now())
                    ->first();

                if (!$auction) {
                    abort(400, __('notifications.auction_not_found', []));
                } else {
                    $property->auction_id = $auction->id;
                }
            }
            static::assignMrcFees($property);
        });

        static::updating(function ($property) {
            $property->updated_by = Auth::id();
            /*if ($property->isDirty(['status']) && $property->approved_at != null) {
                abort(403, __('notifications.immutable_status_property', []));
            }*/

            if ($property->isDirty(['status']) && $property->sale_confirmed_at == null && $property->status === PropertyInformation::STATUS_ACTIVE) {
                abort(403, __('notifications.property_is_not_confirmed', []));
            }

            if ($property->isDirty(['status']) && $property->status === PropertyInformation::STATUS_CONFIRMED && $property->sale_confirmed_at === null) {
                $property->sale_confirmed_at = now();
            }

            if ($property->isDirty(['status']) && $property->status === PropertyInformation::STATUS_APPROVED) {
                $property->approved_at = now();
            }

            if (!Auth::user()->can(Permissions::PERM_CREATE_TRANSACTIONS_PROPERTIES) && $property->isDirty(['transactions', 'transaction_excerpt', 'customer'])) {
                abort(403, __('notifications.unauthorized_to_create_transaction_property', []));
            }

            if ($property->isDirty(['transactions', 'customer']) && $property->status !== PropertyInformation::STATUS_CANCEL) {
                if (isset($property->transactions)) {
                    if (count($property->transactions)) {
                        $property->transaction_date = now();
                        $property->payment_received_by = Auth::id();
                        $property->cancel_reason = __('notifications.property_cancel_default_reason', []);
                        $property->status = PropertyInformation::STATUS_CANCEL;
                    }
                }
            }

            if (
                $property->isDirty(['status'])
                && $property->status !== PropertyInformation::STATUS_ACTIVE
                && $property->status !== PropertyInformation::STATUS_CONFIRMED
                && $property->sale_confirmed_at !== null
            ) {
                $property->sale_confirmed_at = null;
            }
            $auction = $property->auction;
            if ($auction) {
                $now = Carbon::now();
                $registrationsOpenAt = Carbon::parse($auction->listings_registrations_open_at);
                $registrationsCloseAt = Carbon::parse($auction->listings_registrations_close_at);

                if (!($registrationsOpenAt->lte($now) && $registrationsCloseAt->gte($now))) {
                    if (!Auth::user()->can(Permissions::PERM_ACCESS_ALL_FIELDS_PROPERTIES)) {
                        if (!$property->isDirty(['transactions', 'customer'])){
                            abort(400, __('notifications.auction_closed', []));
                        }
                    }
                }
            }
            static::assignMrcFees($property);
        });

        static::retrieved(function (Property $property) {
            // The code in this section is temporary. It must be remove once it has been applied on existing properties in production
            if (isset($property->organization->id)) {
                $auction = Auction::where('organization_id', $property->organization->id)
                    ->whereDate('listings_registrations_open_at', '<=', Carbon::now())
                    ->whereDate('listings_registrations_close_at', '>=', Carbon::now())
                    ->first();
                if (is_null($property->auction_id)) {
                    if ($auction) {
                        $property->auction_id = $auction->id;
                        $property->save();
                    }
                }
            }
            //end
        });
    }

    public static function assignMrcFees($property)
    {
        $totalTaxes = $property->owed_taxes_municipality + $property->owed_taxes_school_board;

        $auctionFee = $property->auction->auctionFees()
            ->where('amount_min', '<=', $totalTaxes)
            ->where(function ($query) use ($totalTaxes) {
                $query->where('amount_max', '>=', $totalTaxes)
                    ->orWhereNull('amount_max');
            })
            ->first();

        if ($auctionFee) {
            $property->mrc_fees = $auctionFee->mrc_fee;
        } else {
            $property->mrc_fees = 0;
        }
    }

    public function getObjectName(): string
    {
        return $this->designation;
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return [];
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $formatForBatchNumber = number_format(intval(preg_replace('/\D/', '', $search)), 0, '.', ' ');
            $query->where('properties.designation', 'LIKE', "%$search%")
                ->orWhere('properties.excerpt', 'LIKE', "%$search%")
                ->orWhere('properties.registration_number', 'LIKE', "%$search%")
                ->orWhere('properties.country', 'LIKE', "%$search%")
                ->orWhere('properties.state', 'LIKE', "%$search%")
                ->orWhere('properties.city', 'LIKE', "%$search%")
                ->orWhere('properties.address', 'LIKE', "%$search%")
                ->orWhere('properties.zipcode', 'LIKE', "%$search%")
                ->orWhere('properties.sold_amount', 'LIKE', "%$search%")
                ->orWhere('properties.batch_numbers', 'LIKE', "%$formatForBatchNumber%")
                ->orWhere('properties.property_number', 'LIKE', "%$search%")
                ->orWhere('properties.acquisition_number', 'LIKE', "%$search%");
        })
            ->orWhereHas('owners', function ($subQuery) use ($search) {
                $subQuery->where('contacts.lastname', 'LIKE', "%$search%")
                    ->orWhere('contacts.firstname', 'LIKE', "%$search%")
                    ->orWhereRaw("CONCAT(contacts.firstname, ' ', contacts.lastname) LIKE ?", ["%$search%"])
                    ->orWhereRaw("CONCAT(contacts.lastname, ' ', contacts.firstname) LIKE ?", ["%$search%"]);

            })
            ->orWhereHas('updatedBy', function ($subQuery) use ($search) {
                $subQuery->where('users.lastname', 'LIKE', "%$search%")
                    ->orWhere('users.firstname', 'LIKE', "%$search%");
            })
            ->orWhereHas('createdBy', function ($subQuery) use ($search) {
                $subQuery->where('users.lastname', 'LIKE', "%$search%")
                    ->orWhere('users.firstname', 'LIKE', "%$search%");
            });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->where('properties.status', $status);
    }

    public function scopeAuction(Builder $query, ?string $auction): Builder
    {
        return $query->where('properties.auction_id', $auction);
    }

    public function scopeOwner($query, $ownerId): Builder
    {
        return $query->whereHas('owners', function (Builder $query) use ($ownerId) {
            $query->where('contacts.id', $ownerId);
        });
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected function batchNumbers(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: function ($value) {
            foreach ($value as &$batch) {
                $batch['value'] = number_format($batch['value'], 0, '.', ' ');
            }

            return json_encode($value);
        }
        );
    }

    protected function transactions(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }

    protected function taxesDue(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function paymentReceivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_received_by');
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_property');
    }

    public function getSubtotal()
    {
        return $this->mrc_fees ?? 0;
    }

    public function getTotalTaxes()
    {
        return $this->owed_taxes_school_board + $this->owed_taxes_municipality;
    }

    public function getPropertyOwners(): string
    {
        return collect($this->owners)->map(function ($owner) {
            return $owner['firstname'] . ' ' . $owner['lastname'];
        })->implode('; ');
    }

    public function getPropertyLocations(): string
    {
        return collect($this->allowedLocations)->filter(function ($location) {
            return $location['is_municipal'];
        })->map(function ($location) {
            return $location['name'];
        })->implode('; ');
    }

    public function getPropertyBatches(): string
    {
        return collect($this->batch_numbers)
            ->pluck('value')
            ->map(function ($value) {
                return $value;
            })
            ->implode('; ');
    }

    public function getTransactionByType($transactionType)
    {
        $transactions = $this->transactions ?? [];

        foreach ($transactions as $transaction) {
            if ($transaction['transaction_type'] === $transactionType) {
                return $transaction;
            }
        }
        return null;
    }

    public function getPaymentMethod()
    {
        $transaction = $this->getTransactionByType(\App\Constants\PropertyInformation::PAYMENT_TYPE_PAYMENT);
        \Illuminate\Support\Facades\Log::info($transaction);
        if (isset($transaction)) {
            return $transaction['method_payment'];
        }
        return null;
    }

    public function getPaymentTotalReceived()
    {
        $transaction = $this->getTransactionByType(\App\Constants\PropertyInformation::PAYMENT_TYPE_PAYMENT);
        if (isset($transaction)) {
            return $transaction['amount'];
        }
        return 0;
    }

    public function getPaymentRefundedAmount()
    {
        $transaction = $this->getTransactionByType(\App\Constants\PropertyInformation::PAYMENT_TYPE_REFUND);
        if (isset($transaction)) {
            return $transaction['amount'];
        }
        return 0;
    }

    public function getPaymentReceiver(): string
    {
        if (isset($this->paymentReceivedBy)) {
            return $this->paymentReceivedBy->firstname . ' ' . $this->paymentReceivedBy->lastname;
        }
        return '';
    }

    public function getTotal()
    {
        $subtotal = $this->getSubtotal();

        $totalTaxes = $this->getTotalTaxes();

        return $subtotal + $totalTaxes;
    }

    public function getAuctionIsClosed(): bool
    {
        if ($this->auction_id) {
            $auction = Auction::where('auctions.id', '=', $this->auction_id)
                ->where('listings_registrations_open_at', '<=', Carbon::now())
                ->where('listings_registrations_close_at', '>=', Carbon::now())
                ->first();

            return !$auction;
        }

        return true;
    }

    public function scopeActive(Builder $query, $value): Builder
    {
        return $query->where('properties.active', '=', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }
}
