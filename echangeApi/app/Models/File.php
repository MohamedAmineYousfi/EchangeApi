<?php

namespace App\Models;

use App\Helpers\File as HelpersFile;
use App\Models\Scopes\DocumentScope;
use App\Support\Interfaces\OnDeleteRelationsCheckable;
use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\OnDeleteRelationsChecked;
use App\Support\Traits\OrganizationScoped;
use App\Support\Traits\Taggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property array<string, mixed> $file_history
 * @property float $size
 */
class File extends Model implements OnDeleteRelationsCheckable, OrganizationScopable
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
        'path',
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
        static::addGlobalScope(new DocumentScope());

        self::onDeleteRelationsCheckedBooted();

        static::creating(function (File $file) {
            $file->owner()->associate(auth()->user());
            $file->file_history = [];
        });

        static::saving(function (File $file) {
            $file->size = Storage::size($file->path);

            /** @var ?User */
            $user = auth()->user();
            if ($user) {
                if ($user->organization) {
                    if (! $file->organization) {
                        $file->organization()->associate($user->organization);
                    } else {
                        if (! $file->organization->is($user->organization)) {
                            abort(403, 'You are not allowed to edit this model');
                        }
                    }
                }
            }
        });

        static::updating(function (File $file) {
            $history = $file->file_history;
            $history[] = [
                'name' => $file->oldAttributes['name'],
                'size' => $file->oldAttributes['size'],
                'human_readable_size' => HelpersFile::getHumanReadableSize($file->oldAttributes['size']),
                'path' => $file->oldAttributes['path'],
                'url' => URL::asset(Storage::url($file->oldAttributes['path']), true),
                'excerpt' => $file->oldAttributes['excerpt'],
                'folder_id' => $file->oldAttributes['folder_id'],
                'updated_at' => $file->updated_at,
            ];
            $file->file_history = $history;
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
     * Get the taxes
     */
    protected function fileHistory(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
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

    public function object(): MorphTo
    {
        return $this->morphTo();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'document', 'documents_roles');
    }

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'document', 'documents_users');
    }

    public function scopeSearch($query, $search): Builder
    {
        return $query->where('files.name', 'LIKE', "%$search%", 'or')
            ->where('files.path', 'LIKE', "%$search%", 'or')
            ->where('files.excerpt', 'LIKE', "%$search%", 'or');
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     */
    public function scopeObjectType($query, $subjectType): Builder
    {
        $type = json_api()->getDefaultResolver()->getType($subjectType);

        return $query->where('files.object_type', $type);
    }
}
