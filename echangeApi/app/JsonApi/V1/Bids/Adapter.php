<?php

namespace App\JsonApi\V1\Bids;

use App\Models\Bid;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Eloquent\BelongsTo;
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
    protected $defaultSort = '-bid';

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
        parent::__construct(new Bid(), $paging);
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
     * Declare user relationship
     */
    protected function user(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare createdBy relationship
     */
    protected function createdBy(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare property relationship
     */
    protected function property(): BelongsTo
    {
        return $this->belongsTo();
    }
}
