<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Property;

class AuctionFee extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use SoftDeletes;

    protected $fillable = [
        'amount_min',
        'amount_max',
        'mrc_fee',
        'auction_id',
    ];

    /**
     * The changed model attributes.
     *
     * @var array
     */
    protected $changes = [
        'amount_min' => 'float',
        'amount_max' => 'float',
        'mrc_fee' => 'float',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();

        static::creating(function ($auctionFee) {
            $lastAuctionFee = static::where('auction_id', $auctionFee->auction_id)
                ->where(function ($query) {
                    $query->whereNull('amount_max')
                        ->orWhereNotNull('amount_max');
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($lastAuctionFee) {
                if (is_null($lastAuctionFee->amount_max)) {
                    abort(422, __('errors.previous_max_amount_null', []));
                }
            }

            if ($auctionFee->amount_max === null) {
                if ($auctionFee->amount_min <= $lastAuctionFee->amount_max) {
                    abort(400, __('errors.interval_overlap_error', []));
                }
            } else {
                $overlappingAuctionFee = static::where('auction_id', $auctionFee->auction_id)
                    ->where('id', '!=', $auctionFee->id)
                    ->where(function ($query) use ($auctionFee) {
                        $query->whereBetween('amount_min', [$auctionFee->amount_min, $auctionFee->amount_max])
                            ->orWhereBetween('amount_max', [$auctionFee->amount_min, $auctionFee->amount_max])
                            ->orWhere(function ($query) use ($auctionFee) {
                                $query->where('amount_min', '<=', $auctionFee->amount_min)
                                    ->where('amount_max', '>=', $auctionFee->amount_max);
                            });
                    })
                    ->first();

                if ($overlappingAuctionFee) {
                    abort(400, __('errors.interval_overlap_error', []));
                }

                if ($auctionFee->amount_max <= $auctionFee->amount_min) {
                    abort(400, __('errors.invalid_current_max_amount', []));
                }
            }
        });

        static::created(function ($auctionFee) {
            static::recalculateFees($auctionFee);
        });

        static::updating(function ($auctionFee) {
            $lastAuctionFee = AuctionFee::where('auction_id', $auctionFee->auction_id)
                ->where('id', '!=', $auctionFee->id)
                ->whereNotNull('amount_max')
                ->orderBy('amount_max', 'desc')
                ->first();

            if ($auctionFee->amount_max === null) {
                if ($auctionFee->amount_min <= $lastAuctionFee->amount_max) {
                    abort(400, __('errors.interval_overlap_error', []));
                }
            } else {
                $overlappingAuctionFee = static::where('auction_id', $auctionFee->auction_id)
                    ->where('id', '!=', $auctionFee->id)
                    ->where(function ($query) use ($auctionFee) {
                        $query->whereBetween('amount_min', [$auctionFee->amount_min, $auctionFee->amount_max])
                            ->orWhereBetween('amount_max', [$auctionFee->amount_min, $auctionFee->amount_max])
                            ->orWhere(function ($query) use ($auctionFee) {
                                $query->where('amount_min', '<=', $auctionFee->amount_min)
                                    ->where('amount_max', '>=', $auctionFee->amount_max);
                            });
                    })
                    ->first();

                if ($overlappingAuctionFee) {
                    abort(400, __('errors.interval_overlap_error', []));
                }
            }
        });

        static::updated(function ($auctionFee) {
            static::recalculateFees($auctionFee);
        });
    }

    protected static function recalculateFees($auctionFee) {
        $auction = $auctionFee->auction;
        if ($auction) {
            $properties = $auction->properties;
            foreach ($properties as $property) {
                Property::assignMrcFees($property);
            }
            $auction->properties()->saveMany($properties);
        }
    }

    public function getObjectName(): string
    {
        return '['.$this->amount_min.' - '.$this->amount_max.']';
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

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }
}
