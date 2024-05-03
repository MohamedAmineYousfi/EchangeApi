<?php

namespace App\JsonApi\V1\ResellerInvoiceItems;

use App\Models\ResellerInvoiceItem;
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
        parent::__construct(new ResellerInvoiceItem(), $paging);
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
    public function resellerInvoiceable(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare invoice relationship
     */
    protected function resellerInvoice(): BelongsTo
    {
        return $this->belongsTo();
    }

    public function taxGroups(): HasMany
    {
        return $this->hasMany();
    }
}
