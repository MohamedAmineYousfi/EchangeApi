<?php

namespace App\JsonApi\V1\Roles;

use App\Models\Role;
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

    protected $guarded = ['id', 'created_at', 'updated_at'];

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
        parent::__construct(new Role(), $paging);
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
     * Declare permissions relationship
     */
    protected function permissions(): HasMany
    {
        return $this->hasMany();
    }

    /**
     * Declare users relationship
     *
     * @return BelongsTo
     */
    protected function reseller()
    {
        return $this->belongsTo();
    }

    /**
     * Declare users relationship
     *
     * @return BelongsTo
     */
    protected function organization()
    {
        return $this->belongsTo();
    }

    /**
     * Declare contactable relationship
     *
     * @return HasMany
     */
    public function allowedLocations()
    {
        return $this->hasMany();
    }
}
