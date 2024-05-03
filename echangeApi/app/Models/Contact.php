<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use App\Support\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $other_phones
 */
class Contact extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity, SoftDeletes;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        OrganizationScoped::booted as organizationScopedBooted;
    }
    use Taggable;

    protected $fillable = [
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

        'title',
        'tags',
        'company_name',
        'school',
        'birthday',
        'excerpt',

        'contactable_id',
        'contactable_type',
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

    public function getObjectName(): string
    {
        return $this->firstname.' '.$this->lastname;
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
        return $query->where(DB::raw('CONCAT(contacts.firstname, " ", contacts.lastname)'), 'LIKE', "%$search%", 'or')
            ->where('contacts.phone', 'LIKE', "%$search%", 'or')
            ->where('contacts.company_name', 'LIKE', "%$search%", 'or')
            ->where('contacts.email', 'LIKE', "%$search%", 'or');
    }

    public function scopeProperty($query, $ownerId): Builder
    {
        return $query->whereHas('properties', function (Builder $query) use ($ownerId) {
            $query->where('properties.id', $ownerId);
        });
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }
}
