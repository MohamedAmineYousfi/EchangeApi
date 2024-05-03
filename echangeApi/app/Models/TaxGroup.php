<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $other_phones
 */
class TaxGroup extends Model implements EventNotifiableContract, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity, SoftDeletes;
    use OrganizationScoped {
        OrganizationScoped::booted as organizationScopedBooted;
    }

    protected $fillable = [
        'name',
        'country_code',
        'excerpt',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::organizationScopedBooted();
        self::eventNotifiableBooted();

        self::saved(function (TaxGroup $taxGroup) {
            $request = request();
            if (! isset($request['data'])) {
                return;
            }
            $data = $request['data'];
            if (! isset($data['relationships'])) {
                return;
            }
            $relationships = $data['relationships'];
            if (! isset($relationships['taxes'])) {
                return;
            }
            $taxes = $relationships['taxes'];
            if (! isset($taxes['data'])) {
                return;
            }

            foreach ($taxes['data'] as $key => $taxData) {
                $taxGroup->taxes()->updateExistingPivot($taxData['id'], ['seq_number' => $key]);
            }
        });
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function isLocationRestricted(): bool
    {
        return false;
    }

    public function getObjectName(): string
    {
        return $this->name;
    }

    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'taxes_tax_groups')->withPivot('seq_number')->orderBy('seq_number');
    }

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('tax_groups.name', 'LIKE', "%$search%");
    }

    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('tax_groups.id', $ids);
    }

    public function scopeIdsNotIn(Builder $query, ?array $ids): Builder
    {
        return $query->whereNotIn('tax_groups.id', $ids);
    }
}
