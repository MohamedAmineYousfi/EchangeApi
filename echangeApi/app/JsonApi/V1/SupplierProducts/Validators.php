<?php

namespace App\JsonApi\V1\SupplierProducts;

use App\Rules\SupplierProduct;
use CloudCreativity\LaravelJsonApi\Rules\HasMany;
use CloudCreativity\LaravelJsonApi\Rules\HasOne;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;

class Validators extends AbstractValidators
{
    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *                    the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = ['supplier', 'product', 'taxGroups'];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = ['sku', 'price', 'created_at'];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = ['search', 'supplier', 'product', 'status', 'latestProduct'];

    /**
     * Get resource validation rules.
     *
     * @param  mixed|null  $record
     *                              the record being updated, or null if creating a resource.
     * @param  array  $data
     *                       the data being validated
     */
    protected function rules($record, array $data): array
    {
        return [
            'sku' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'buying_price' => ['required', 'numeric'],
            'selling_price' => ['required', 'numeric'],
            'custom_pricing' => ['required', 'boolean'],
            'custom_taxation' => ['required', 'boolean'],
            'product' => [
                'required',
                new HasOne('products'),
            ],
            'supplier' => [
                'required',
                new HasOne('suppliers'),
                new SupplierProduct(),
            ],
            'taxGroups' => [
                new HasMany('tax-groups'),
            ],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.search' => 'filled|string',
            'filter.product' => 'filled|string',
            'filter.supplier' => 'filled|string',
        ];
    }
}
