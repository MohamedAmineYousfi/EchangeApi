<?php

namespace App\JsonApi\V1\PurchasesDeliveries;

use App\Models\PurchasesDelivery;
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
        parent::__construct(new PurchasesDelivery(), $paging);
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
     * Declare purchasesDelivery relationship
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare purchasesDelivery relationship
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare purchasesDelivery relationship
     */
    public function purchasesOrder(): BelongsTo
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
    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo();
    }
}
