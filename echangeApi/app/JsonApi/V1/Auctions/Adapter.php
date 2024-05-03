<?php

namespace App\JsonApi\V1\Auctions;

use App\Models\Auction;
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
        parent::__construct(new Auction(), $paging);
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
     * Declare users relationship
     */
    protected function organization(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare contactable relationship
     */
    public function allowedLocations(): HasMany
    {
        return $this->hasMany();
    }

    /**
     * Declare contactable relationship
     */
    public function managers(): HasMany
    {
        return $this->hasMany();
    }

    /**
     * Declare auction fees relationship
     */
    public function auctionFees(): HasMany
    {
        return $this->hasMany();
    }

    /**
     * Declare bid steps relationship
     */
    public function bidSteps(): HasMany
    {
        return $this->hasMany();
    }
}
