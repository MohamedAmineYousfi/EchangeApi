<?php

namespace App\JsonApi\V1\Categories;

use App\Models\Category;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Eloquent\BelongsTo;
use CloudCreativity\LaravelJsonApi\Eloquent\HasMany;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Adapter extends AbstractAdapter
{
    /**
     * Mapping of JSON API attribute field names to model keys.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $defaultSort = '-created_at';

    /**
     * Mapping of JSON API filter names to model scopes.
     *
     * @var array
     */
    protected $filterScopes = [];

    /**
     * Adapter constructor.
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new Category(), $paging);
    }

    /**
     * @param  Builder  $query
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        $this->filterWithScopes($query, $filters);
    }

    /**
     * Declare Customers relationship
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare Customers relationship
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare Customers relationship
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany();
    }
}
