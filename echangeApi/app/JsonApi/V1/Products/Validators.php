<?php

namespace App\JsonApi\V1\Products;

use App\Constants\ProductsInformation;
use App\Rules\AllowedLocations;
use CloudCreativity\LaravelJsonApi\Rules\HasMany;
use CloudCreativity\LaravelJsonApi\Rules\HasOne;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;
use Illuminate\Validation\Rule;

class Validators extends AbstractValidators
{
    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *                    the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = [
        'organization',
        'organization.reseller',
        'allowedLocations',
        'supplierProducts',
        'warehouseProducts',
        'categories',
        'taxGroups',
        'unitOfMeasure',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'created_at',
        'name',
        'selling_price',
        'buying_price',
        'code',
        'sku',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'search',
        'organization',
        'organization.reseller',
        'allowedLocations',
        'id',
        'ids',
        'supplier',
        'warehouse',
        'status',
        'idsNotIn',
        'latestProduct',
        'supplierProduct',
        'productsNotInWarehouse',
        'hierarchical_category_association',
        'in_categories',
        'in_category',
        'can_be_sold',
        'can_be_purchased',
        'productsNotInSupplier',
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
        if ($record) {
            return [
                'name' => ['required', 'string', 'min:3', 'max:128'],
                'excerpt' => ['nullable', 'string'],
                'sku' => ['nullable', 'string', Rule::unique('products')->ignore($record->id)],
                'buying_price' => ['required', 'numeric'],
                'selling_price' => ['required', 'numeric'],
                'picture' => ['nullable', 'string'],
                'gallery' => ['nullable', 'array'],
                'status' => ['nullable', 'string', 'in:'.implode(',', ProductsInformation::STATUS)],
                'gallery.*' => ['url', 'distinct'],
                'can_be_sold' => ['required', 'boolean'],
                'can_be_purchased' => ['required', 'boolean'],
                'organization' => ['nullable', new HasOne('organizations')],
                'allowedLocations' => [
                    new AllowedLocations(),
                    new HasMany('locations'),
                ],
                'categories' => [
                    new HasMany('categories'),
                ],
                'taxGroups' => [
                    new HasMany('tax-groups'),
                ],
                'unitOfMeasure' => [
                    'required',
                    new HasOne('unit-of-measures'),
                ],
            ];
        }

        return [
            'name' => ['required', 'string', 'min:3', 'max:128'],
            'excerpt' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', Rule::unique('products')],
            'buying_price' => ['required', 'numeric'],
            'selling_price' => ['required', 'numeric'],
            'picture' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'in:'.implode(',', ProductsInformation::STATUS)],
            'gallery.*' => ['url', 'distinct'],
            'can_be_sold' => ['required', 'boolean'],
            'can_be_purchased' => ['required', 'boolean'],
            'organization' => ['nullable', new HasOne('organizations')],
            'allowedLocations' => [
                new AllowedLocations(),
                new HasMany('locations'),
            ],
            'categories' => [
                new HasMany('categories'),
            ],
            'taxGroups' => [
                new HasMany('tax-groups'),
            ],
            'unitOfMeasure' => [
                'required',
                new HasOne('unit-of-measures'),
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
            'filter.organization' => 'filled|string',
            'filter.reseller' => 'filled|string',
            'filter.latestProduct' => 'filled',
            'filter.status' => ['filled', 'string',  'in:'.implode(',', ProductsInformation::STATUS)],
        ];
    }
}
