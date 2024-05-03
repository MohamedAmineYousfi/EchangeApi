<?php

namespace App\Models;

use App\Constants\BillingInformations;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\ModelIsBillableTo;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\Contactable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\ModelHasBillingInformations;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use App\Support\Traits\Taggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $other_phones
 */
class Customer extends Model implements EventNotifiableContract, ModelIsBillableTo, OnDeleteRelationsCheckable, OrganizationScopable
{
    use Contactable;
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity;
    use ModelHasBillingInformations {
        \App\Support\Traits\ModelHasBillingInformations::booted as modelHasBillingInformationsBooted;
    }
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use SoftDeletes;
    use Taggable;

    public const CUSTOMER_TYPE_INDIVIDUAL = 'INDIVIDUAL';

    public const CUSTOMER_TYPE_COMPANY = 'COMPANY';

    protected $fillable = [
        'customer_type',
        'company_name',
        'firstname',
        'lastname',
        'email',
        'phone',
        'phone_extension',
        'phone_type',
        'other_phones',
        'country',
        'state',
        'city',
        'zipcode',
        'address',
        'organization_id',

        ...BillingInformations::MODEL_BILLING_INFORMATIONS_FILLABLES,
    ];

    protected $dates = [
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
        self::modelHasBillingInformationsBooted();

        static::saving(function (Customer $model) {
            if ($model->customer_type === Customer::CUSTOMER_TYPE_COMPANY) {
                $model->firstname = 'N/A';
                $model->lastname = 'N/A';
            } elseif ($model->customer_type === Customer::CUSTOMER_TYPE_INDIVIDUAL) {
                $model->company_name = 'N/A';
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['contacts'];
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
        return $this->getCustomerName();
    }

    public function getCustomerName(): string
    {
        if ($this->customer_type === self::CUSTOMER_TYPE_INDIVIDUAL) {
            return $this->firstname.' '.$this->lastname;
        } elseif ($this->customer_type === self::CUSTOMER_TYPE_COMPANY) {
            return $this->company_name;
        }

        return 'INVALID_TYPE';
    }

    /**
     * Get the other phones
     */
    protected function otherPhones(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(DB::raw('CONCAT(customers.firstname, " ", customers.lastname)'), 'LIKE', "%$search%", 'or')
            ->where('customers.phone', 'LIKE', "%$search%", 'or')
            ->where('customers.email', 'LIKE', "%$search%", 'or');
    }
}
