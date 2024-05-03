<?php

namespace App\Models;

use App\Support\Interfaces\EventNotifiableContract;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\EventNotifiable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * {@inheritDoc}
 *
 * @property string $name
 * @property string $unit_type
 * @property float $ratio
 * @property UnitOfMeasure $unitOfMeasure
 */
class UnitOfMeasureUnit extends Model implements EventNotifiableContract, OnDeleteRelationsCheckable, OrganizationScopable
{
    use EventNotifiable {
        \App\Support\Traits\EventNotifiable::booted as eventNotifiableBooted;
    }
    use HasFactory;
    use LogsActivity;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }
    use SoftDeletes;

    public const TYPE_REFERENCE_UNIT = 'REFERENCE_UNIT';

    public const TYPE_BIGGER_THAN_REFERENCE = 'BIGGER_THAN_REFERENCE';

    public const TYPE_SMALLER_THAN_REFERENCE = 'SMALLER_THAN_REFERENCE';

    protected $fillable = [
        'name',
        'unit_type',
        'ratio',
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

        self::saving(function (UnitOfMeasureUnit $unitOfMeasureUnit) {
            $unitOfMeasureUnit->organization()->associate($unitOfMeasureUnit->unitOfMeasure->organization);
            if ($unitOfMeasureUnit->unit_type == UnitOfMeasureUnit::TYPE_REFERENCE_UNIT) {
                $unitOfMeasureUnit->ratio = 1;
            }
        });

        self::updating(function (UnitOfMeasureUnit $unitOfMeasureUnit) {
            if ($unitOfMeasureUnit->getOriginal('unit_type') === UnitOfMeasureUnit::TYPE_REFERENCE_UNIT) {
                abort(400, __('errors.unit_of_measure_must_have_one_reference_unit'));
            }

            if ($unitOfMeasureUnit->getOriginal('unit_type') != $unitOfMeasureUnit->unit_type) {
                if ($unitOfMeasureUnit->unit_type == UnitOfMeasureUnit::TYPE_REFERENCE_UNIT) {
                    $oldReferenceUnit = $unitOfMeasureUnit->unitOfMeasure->getReferenceUnit();
                    if ($unitOfMeasureUnit->getOriginal('unit_type') == UnitOfMeasureUnit::TYPE_BIGGER_THAN_REFERENCE) {
                        $oldReferenceUnit->unit_type = UnitOfMeasureUnit::TYPE_SMALLER_THAN_REFERENCE;
                    }
                    if ($unitOfMeasureUnit->getOriginal('unit_type') == UnitOfMeasureUnit::TYPE_SMALLER_THAN_REFERENCE) {
                        $oldReferenceUnit->unit_type = UnitOfMeasureUnit::TYPE_BIGGER_THAN_REFERENCE;
                    }
                    $oldReferenceUnit->ratio = $unitOfMeasureUnit->getOriginal('ratio');
                    $oldReferenceUnit->saveQuietly();
                }
            }
        });
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
        return $this->name;
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class);
    }

    /**
     * Get the other phones
     */
    protected function units(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('unit_of_measures.id', $ids);
    }

    /**
     * @param  $name
     */
    public function scopeUnitOfMeasure(Builder $query, ?string $id): Builder
    {
        return $query->where('unit_of_measure_units.unit_of_measure_id', $id);
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where('unit_of_measure_units.name', 'LIKE', "%$search%", 'or');
    }
}
