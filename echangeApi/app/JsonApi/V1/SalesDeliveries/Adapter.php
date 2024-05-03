<?php

namespace App\JsonApi\V1\SalesDeliveries;

use App\Models\SalesDelivery;
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
    protected $filterScopes = [
        'created_at' => 'createdAtBetween',
    ];

    /**
     * Adapter constructor.
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new SalesDelivery(), $paging);
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
     * Declare salesDelivery relationship
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare salesDelivery relationship
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare salesDelivery relationship
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare Items relationship
     */
    public function items(): HasMany
    {
        return $this->hasMany();
    }

    /**
     * Declare contactable relationship
     */
    public function allowedLocations(): HasMany
    {
        return $this->hasMany();
    }

    /**
     * Declare salesDelivery relationship
     */
    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo();
    }
}
