<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\ResellerInvoiceable;
use App\Support\Interfaces\ResellerScopable;
use App\Support\Interfaces\TaxableItem;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\HasTaxGroups;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\ResellerScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property mixed $gallery
 * @property float $amount
 */
class Package extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, ResellerInvoiceable, ResellerScopable, TaxableItem
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use HasTaxGroups;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use ResellerScoped {
        \App\Support\Traits\ResellerScoped::booted as resellerScopedBooted;
    }
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'excerpt',
        'price',
        'picture',
        'gallery',
        'frequency',
        'maximum_users',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::resellerScopedBooted();
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();

        static::creating(function (Package $package) {
            $invoicesCount = Package::withoutGlobalScopes()->count() + 1;
            $package->code = 'PKG-'.Carbon::now()->format('Ymd').str_pad(strval($invoicesCount), 3, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['subscriptions', 'invoiceItems'];
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
        return $this->name;
    }

    /**
     * Undocumented function
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Undocumented function
     */
    public function invoiceItems(): MorphMany
    {
        return $this->morphMany(ResellerInvoiceItem::class, 'reseller_invoiceable');
    }

    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    public function defaultRole(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where('packages.name', 'LIKE', "%$search%", 'or')
            ->where('packages.code', 'LIKE', "%$search%", 'or');
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('packages.id', $ids);
    }

    /**
     * @param  $name
     */
    public function scopeId(Builder $query, ?string $id): Builder
    {
        return $query->where('packages.id', '=', $id);
    }

    /**
     * getDenomination
     */
    public function getDenomination(): string
    {
        return "$this->code - $this->name";
    }

    public function handleResellerInvoicePaied(ResellerInvoiceItem $resellerInvoiceItem): void
    {
        $resellerInvoice = $resellerInvoiceItem->getInvoice();
        $organization = $resellerInvoice->recipient;
        $subscription = Subscription::where('organization_id', '=', $organization->id)
            ->where('package_id', '=', $this->id)
            ->first();
        if (! $subscription) {
            $subscription = new Subscription([
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now(),
            ]);
            $subscription->package()->associate($this);
            $subscription->organization()->associate($organization);
        }

        $endTime = new Carbon($subscription->end_time) > Carbon::now() ?
            (new Carbon($subscription->end_time))->add(str_repeat($this->frequency, $resellerInvoiceItem->quantity))
            : Carbon::now()->add(str_repeat($this->frequency, $resellerInvoiceItem->quantity));

        $subscription->end_time = $endTime;
        $subscription->save();
    }

    public function handleResellerInvoiceValidated(ResellerInvoiceItem $resellerInvoiceItem): void
    {
    }

    public function handleResellerInvoiceCanceled(ResellerInvoiceItem $resellerInvoiceItem): void
    {
    }
}
