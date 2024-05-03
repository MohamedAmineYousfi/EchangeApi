<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $name
 * @property string $value_type
 * @property string $value
 * @property int $organization_id
 * @property array<string, mixed> $data
 */
class Option extends Model implements EventNotifiableContract, OrganizationScopable
{
    const OPTION_TYPE_NUMBER = 'NUMBER';

    const OPTION_TYPE_STRING = 'STRING';

    const OPTION_TYPE_WYSIWYG = 'WYSIWYG';

    const OPTION_TYPE_OBJECT = 'OBJECT';

    const OPTION_TYPE_ARRAY = 'ARRAY';

    const OPTION_TYPE_CHECKBOX = 'CHECKBOX';

    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use LogsActivity;
    use OrganizationScoped {
        OrganizationScoped::booted as organizationScopedBooted;
    }

    protected $fillable = [
        'organization_id',
        'name',
        'module',
        'value_type',
        'value',
        'data',
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

    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Get the other phones
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                switch ($this->value_type) {
                    case self::OPTION_TYPE_NUMBER:
                        return floatval($value);
                    case self::OPTION_TYPE_STRING:
                        return strval($value);
                    case self::OPTION_TYPE_WYSIWYG:
                        return strval($value);
                    case self::OPTION_TYPE_OBJECT:
                        return json_decode($value);
                    case self::OPTION_TYPE_ARRAY:
                        return json_decode($value);
                    case self::OPTION_TYPE_CHECKBOX:
                        return boolval($value);
                }
            },
            set: function ($value) {
                switch ($this->value_type) {
                    case self::OPTION_TYPE_NUMBER:
                        return strval($value);
                    case self::OPTION_TYPE_STRING:
                        return strval($value);
                    case self::OPTION_TYPE_WYSIWYG:
                        return strval($value);
                    case self::OPTION_TYPE_OBJECT:
                        return json_decode($value, true);
                    case self::OPTION_TYPE_ARRAY:
                        return json_decode($value, true);
                    case self::OPTION_TYPE_CHECKBOX:
                        return boolval($value);
                }
            },
        );
    }

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(DB::raw('options.name, " ", options.type, " ", options.value)'), 'LIKE', "%$search%");
    }
}
