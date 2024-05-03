<?php

namespace App\JsonApi\V1\PurchasesPayments;

use App\Models\PurchasesPayment;
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
    protected $filterScopes = [
        'date' => 'dateBetween',
    ];

    /**
     * Adapter constructor.
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new PurchasesPayment(), $paging);
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
     * Declare invoice relationship
     */
    protected function invoice(): BelongsTo
    {
        return $this->belongsTo();
    }

    /**
     * Declare invoice relationship
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
}
