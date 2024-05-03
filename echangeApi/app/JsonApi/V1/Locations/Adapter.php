<?php

namespace App\JsonApi\V1\Locations;

use App\Models\Location;
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
        parent::__construct(new Location(), $paging);
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
     * Declare Locations relationship
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare Locations relationship
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare contactable relationship
     */
    public function contacts(): HasMany
    {
        return $this->hasMany();
    }

    /**
     * Declare contactable relationship
     */
    public function users(): HasMany
    {
        return $this->hasMany();
    }
}
