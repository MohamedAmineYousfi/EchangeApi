<?php

namespace App\Models;

use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array $mapping
 * @property mixed $identifier
 * @property array $results
 */
class Import extends Model implements OnDeleteRelationsCheckable, OrganizationScopable
{
    use LogsActivity, SoftDeletes;
    use OnDeleteRelationsChecked {
        OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        OrganizationScoped::booted as organizationScopedBooted;
    }

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_RUNNING = 'RUNNING';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELED = 'CANCELED';

    protected $fillable = [
        'name',
        'excerpt',
        'mapping',
        'file_url',
        'model',
        'results',
        'done',
        'organization_id',
        'status',
        'linked_object_id',
        'linked_object_type',
        'identifier',
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
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('imports.name', 'LIKE', "%$search%");
    }

    /**
     * Get headerMapping
     */
    protected function mapping(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    protected function results(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    public function getRelationsMethods(): array
    {
        return [];
    }

    public function getImportedItems(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'importable', 'importables', 'import_id', 'importable_id');
    }

    public function importedItems(): MorphTo
    {
        return $this->morphTo();
    }

    public function linkedObject(): MorphTo
    {
        return $this->morphTo();
    }

    public function linked_object(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeLinkedObjectId(Builder $query, string $objetId): Builder
    {
        return $query->where('imports.linked_object_id', $objetId);
    }

    public function scopeLinkedObjectType(Builder $query, string $objectType): Builder
    {
        return $query->where('imports.linked_object_type', $objectType);
    }
}
