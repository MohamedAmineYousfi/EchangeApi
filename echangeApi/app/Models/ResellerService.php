<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\ResellerInvoiceable;
use App\Support\Interfaces\ResellerScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\ResellerScoped;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $code
 * @property string $name
 * @property string $excerpt
 * @property float $price
 * @property string $picture
 * @property mixed $gallery
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class ResellerService extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, ResellerInvoiceable, ResellerScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
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

        static::saving(function (ResellerService $service) {
            if (! $service->code) {
                $invoicesCount = ResellerService::withoutGlobalScopes()->count() + 1;
                $service->code = 'R-SRV-'.Carbon::now()->format('Ymd').str_pad(strval($invoicesCount), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['invoiceItems'];
    }

    public function getObjectName(): string
    {
        return $this->code.' '.$this->name;
    }

    /**
     * Undocumented function
     */
    public function invoiceItems(): MorphMany
    {
        return $this->morphMany(ResellerInvoiceItem::class, 'reseller_invoiceable');
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected function gallery(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where('reseller_services.name', 'LIKE', "%$search%", 'or')
            ->where('reseller_services.code', 'LIKE', "%$search%", 'or');
    }

    public function getDenomination(): string
    {
        return "$this->code - $this->name";
    }

    public function handleResellerInvoiceValidated(ResellerInvoiceItem $resellerInvoiceItem): void
    {
    }

    public function handleResellerInvoicePaied(ResellerInvoiceItem $resellerInvoiceItem): void
    {
    }

    public function handleResellerInvoiceCanceled(ResellerInvoiceItem $resellerInvoiceItem): void
    {
    }
}
