<?php

namespace App\Models;

use App\Constants\BillingInformations;
use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\ModelIsBillableTo;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\Contactable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\LinkedObject;
use App\Support\Traits\ModelHasBillingInformations;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use App\Support\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $other_phones
 */
class Supplier extends Model implements EventNotifiableContract, ModelIsBillableTo, OnDeleteRelationsCheckable, OrganizationScopable
{
    use Contactable;
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LinkedObject;
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

    protected $fillable = [
        'country',
        'state',
        'city',
        'zipcode',
        'address',
        'company_name',
        'tags',
        'email',
        'phone',
        'phone_extension',
        'phone_type',
        'other_phones',
        'fiscal_number',
        'excerpt',

        ...BillingInformations::MODEL_BILLING_INFORMATIONS_FILLABLES,
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
        self::onDeleteRelationsCheckedBooted();
        self::eventNotifiableBooted();
        self::modelHasBillingInformationsBooted();
    }

    /**
     * Undocumented function
     */
    public function getRelationsMethods(): array
    {
        return ['contacts', 'imports'];
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
        return $this->company_name;
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

    public function supplierProducts(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->whereHas('supplierProducts', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        });
    }

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query
            ->where('suppliers.company_name', 'LIKE', "%$search%", 'or')
            ->where('suppliers.email', 'LIKE', "%$search%", 'or')
            ->where('suppliers.fiscal_number', 'LIKE', "%$search%", 'or')
            ->where('suppliers.phone', 'LIKE', "%$search%", 'or');
    }

    public function scopeIdsNotIn(Builder $query, ?array $ids): Builder
    {
        return $query->whereNotIn('suppliers.id', $ids);
    }
}
