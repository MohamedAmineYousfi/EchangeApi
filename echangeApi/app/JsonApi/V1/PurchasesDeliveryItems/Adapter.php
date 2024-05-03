<?php

namespace App\JsonApi\V1\PurchasesDeliveryItems;

use App\Models\PurchasesDeliveryItem;
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
        parent::__construct(new PurchasesDeliveryItem(), $paging);
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
     * An Event belongs to a User
     */
    public function purchasesDelivery(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare purchasesPurchasesDelivery relationship
     */
    protected function purchasesDeliverable(): BelongsTo
    {
        return $this->belongsTo();
    }
}
