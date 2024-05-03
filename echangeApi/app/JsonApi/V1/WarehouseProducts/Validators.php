<?php

namespace App\JsonApi\V1\WarehouseProducts;

use App\Rules\WarehouseProductUniqueProduct;
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
    protected $allowedIncludePaths = [
        'warehouse',
        'product',
        'supplier',
        'taxGroups',
        'unitOfMeasureUnit',
        'unitOfMeasureUnit.unitOfMeasure',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'quantity',
        'selling_price',
        'buying_price',
        'created_at',
        'product.status',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'warehouse',
        'product',
        'products',
        'supplier',
        'id',
        'search',
        'status',
        'latestProduct',
    ];

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
            'buying_price' => ['required', 'numeric'],
            'selling_price' => ['required', 'numeric'],
            'custom_pricing' => ['required', 'boolean'],
            'custom_taxation' => ['required', 'boolean'],
            'product' => [
                'required',
                new HasOne('products'),
                new WarehouseProductUniqueProduct(),
            ],
            'warehouse' => [
                'required',
                new HasOne('warehouses'),
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
            'filter.created_at' => 'array|min:2',
            'filter.created_at.*' => 'filled|date_format:Y-m-d H:i:s',
            'filter.search' => 'string',
            'filter.products' => 'array',
            'filter.id' => 'string',
        ];
    }
}
