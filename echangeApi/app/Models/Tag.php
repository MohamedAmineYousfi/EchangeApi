<?php

namespace App\Models;

use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model implements OrganizationScopable
{
    use HasFactory;
    use OrganizationScoped {
        \App\Support\Traits\OrganizationScoped::booted as organizationScopedBooted;
    }

    protected $fillable = [
        'name',
        'color',
        'slug',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [];

    public function isLocationRestricted(): bool
    {
        return false;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::organizationScopedBooted();

        static::saving(function (Tag $model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function scopeName(Builder $query, ?string $name): Builder
    {
        return $query->where('tags.name', 'LIKE', '%'.$name.'%');
    }

    /**
     * @param  $name
     */
    public function scopeIds(Builder $query, ?array $ids): Builder
    {
        return $query->whereIn('tags.id', $ids);
    }
}
