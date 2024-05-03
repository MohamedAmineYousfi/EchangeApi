<?php

namespace App\Models;

use App\Models\Scopes\DocumentScope;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use App\Support\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Folder extends Model implements OnDeleteRelationsCheckable, OrganizationScopable
{
    use LogsActivity, SoftDeletes;
    use OnDeleteRelationsChecked {
        \App\Support\Traits\OnDeleteRelationsChecked::booted as onDeleteRelationsCheckedBooted;
    }
    use OrganizationScoped {
        OrganizationScoped::booted as organizationScopedBooted;
    }
    use Taggable;

    protected $fillable = [
        'name',
        'excerpt',
        'locked',
        'organization_id',
        'parent_id',
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
        static::addGlobalScope(new DocumentScope());

        self::onDeleteRelationsCheckedBooted();

        self::creating(function (Folder $model) {
            $model->owner()->associate(auth()->user());
        });

        static::saving(function (Folder $model) {
            /** @var ?User */
            $user = auth()->user();
            if ($user) {
                if ($user->organization) {
                    if (! $model->organization) {
                        $model->organization()->associate($user->organization);
                    } else {
                        if (! $model->organization->is($user->organization)) {
                            abort(403, 'You are not allowed to edit this model');
                        }
                    }
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function subfolders(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'document', 'documents_roles');
    }

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'document', 'documents_users');
    }

    /**
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('folders.name', 'LIKE', "%$search%", 'or')
            ->where('folders.excerpt', 'LIKE', "%$search%", 'or');
    }

    /**
     * @param  $search
     * @return mixed
     */
    public function scopeParent($query, $parent = null)
    {
        if ($parent) {
            return $query->whereNull('folders.parent_id');
        } else {
            return $query->where('folders.parent_id', '=', $parent);
        }
    }
}
