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

class BidStep extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable
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
        'bid_amount',
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
        'bid_amount' => 'float',
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

        static::creating(function ($bidStep) {
            $lastBidStep = static::where('auction_id', $bidStep->auction_id)
                ->where(function ($query) {
                    $query->whereNull('amount_max')
                        ->orWhereNotNull('amount_max');
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($lastBidStep) {
                if (is_null($lastBidStep->amount_max)) {
                    abort(400, __('errors.previous_max_amount_null', []));
                }
            }

            if ($bidStep->amount_max === null) {
                if ($bidStep->amount_min <= $lastBidStep->amount_max) {
                    abort(400, __('errors.interval_overlap_error', []));
                }
            } else {
                $overlappingBidStep = static::where('auction_id', $bidStep->auction_id)
                    ->where('id', '!=', $bidStep->id)
                    ->where(function ($query) use ($bidStep) {
                        $query->whereBetween('amount_min', [$bidStep->amount_min, $bidStep->amount_max])
                            ->orWhereBetween('amount_max', [$bidStep->amount_min, $bidStep->amount_max])
                            ->orWhere(function ($query) use ($bidStep) {
                                $query->where('amount_min', '<=', $bidStep->amount_min)
                                    ->where('amount_max', '>=', $bidStep->amount_max);
                            });
                    })
                    ->first();

                if ($overlappingBidStep) {
                    abort(400, __('errors.interval_overlap_error', []));
                }

                if ($bidStep->amount_max <= $bidStep->amount_min) {
                    abort(400, __('errors.invalid_current_max_amount', []));
                }
            }
        });

        static::updating(function ($bidStep) {

            $lastBidStep = BidStep::where('auction_id', $bidStep->auction_id)
                ->where('id', '!=', $bidStep->id)
                ->whereNotNull('amount_max')
                ->orderBy('amount_max', 'desc')
                ->first();

            if ($bidStep->amount_max === null) {
                if ($bidStep->amount_min <= $lastBidStep->amount_max) {
                    abort(422, __('errors.interval_overlap_error', []));
                }
            } else {
                $overlappingBidStep = static::where('auction_id', $bidStep->auction_id)
                    ->where('id', '!=', $bidStep->id)
                    ->where(function ($query) use ($bidStep) {
                        $query->whereBetween('amount_min', [$bidStep->amount_min, $bidStep->amount_max])
                            ->orWhereBetween('amount_max', [$bidStep->amount_min, $bidStep->amount_max])
                            ->orWhere(function ($query) use ($bidStep) {
                                $query->where('amount_min', '<=', $bidStep->amount_min)
                                    ->where('amount_max', '>=', $bidStep->amount_max);
                            });
                    })
                    ->first();

                if ($overlappingBidStep) {
                    abort(422, __('errors.interval_overlap_error', []));
                }
            }
        });
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
