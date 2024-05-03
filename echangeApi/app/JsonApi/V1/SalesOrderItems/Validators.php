<?php

namespace App\JsonApi\V1\SalesOrderItems;

use App\Models\SalesOrder;
use App\Rules\UniqueLineItemOf;
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
    protected $allowedIncludePaths = ['taxGroups'];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'order',
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
            'code' => ['required', 'string', 'min:3', 'max:128'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'unit_price' => ['required', 'numeric'],
            'quantity' => ['required', 'integer', 'min:1'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
            'salesOrder' => [
                'required',
                new HasOne('sales-orders'),
            ],
            'salesOrderable' => [
                'required',
                new HasOne('products', 'supplier-products', 'warehouse-products'),
                new UniqueLineItemOf(SalesOrder::class, 'salesOrder', 'salesOrderable'),
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
            'filter.order' => 'string',
        ];
    }
}
