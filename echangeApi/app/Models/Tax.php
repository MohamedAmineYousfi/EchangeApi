<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property bool $active
 * @property string $name
 * @property string $label
 * @property string $tax_number
 * @property string $tax_type
 * @property string $calculation_type
 * @property string $calculation_base
 * @property float $value
 */
class Tax extends Model implements EventNotifiableContract, OrganizationScopable
{
    const TAX_CALCULATION_TYPE_AMOUNT = 'AMOUNT';

    const TAX_CALCULATION_TYPE_PERCENTAGE = 'PERCENTAGE';

    const TAX_TYPE_SALES = 'SALES';

    const TAX_TYPE_PURCHASES = 'PURCHASES';

    const TAX_CALCULATION_BASE_BEFORE_TAX = 'BEFORE_TAX';

    const TAX_CALCULATION_BASE_AFTER_TAX = 'AFTER_TAX';

    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity, SoftDeletes;
    use OrganizationScoped {
        OrganizationScoped::booted as organizationScopedBooted;
    }

    protected $fillable = [
        'active',
        'name',
        'label',
        'tax_number',
        'tax_type',
        'calculation_type',
        'calculation_base',
        'value',
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

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%$search%")
            ->orWhere('label', 'LIKE', "%$search%")
            ->orWhere('tax_number', 'LIKE', "%$search%");
    }
}
