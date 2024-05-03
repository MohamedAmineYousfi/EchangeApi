<?php

namespace App\Models;

use App\Support\Interfaces\OrganizationScopable;
use App\Support\Traits\OrganizationScoped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model implements OrganizationScopable
{
    use HasFactory;
    use LogsActivity;
    use OrganizationScoped {
        OrganizationScoped::booted as organizationScopedBooted;
    }

    protected $fillable = [
        'name',
        'excerpt',
        'parent_id',
        'color',
        'icon',
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
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    public function scopeInParentCategory(Builder $query, $parentId): Builder
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeInCategories(Builder $query, array $categoryIds): Builder
    {
        $subCategories = Category::whereIn('parent_id', $categoryIds)->pluck('id')->toArray();

        while (! empty($subCategories)) {
            $categoryIds = array_merge($categoryIds, $subCategories);
            $subCategories = Category::whereIn('parent_id', $subCategories)->pluck('id')->toArray();
        }

        return $query->whereIn('id', array_unique($categoryIds));
    }

    public function scopeIdsNotIn(Builder $query, ?array $ids): Builder
    {
        return $query->whereNotIn('categories.id', $ids);
    }

    public function scopeId(Builder $query, string $id): Builder
    {
        return $query->where('categories.id', $id);
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where('categories.name', 'LIKE', "%$search%", 'or')
            ->orWhere('categories.excerpt', 'LIKE', "%$search%", 'or');
    }

    public function scopeName(Builder $query, ?string $name): Builder
    {
        return $query->where('categories.name', 'LIKE', '%'.$name.'%');
    }

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
