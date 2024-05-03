<?php

namespace App\JsonApi\V1\SalesDeliveryItems;

use App\Models\SalesDelivery;
use App\Rules\SalesDeliveryOrderItem;
use App\Rules\UniqueLineItemOf;
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
    protected $allowedIncludePaths = [];

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
        'sales-delivery',
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
            'quantity' => ['required', 'integer', 'min:0', new SalesDeliveryOrderItem()],
            'expected_quantity' => ['required', 'integer', 'min:0', new SalesDeliveryOrderItem()],
            'salesDelivery' => [
                'required',
                new HasOne('sales-deliveries'),
            ],
            'salesDeliverable' => [
                'required',
                new HasOne('products', 'supplier-products', 'warehouse-products'),
                new UniqueLineItemOf(SalesDelivery::class, 'salesDelivery', 'salesDeliverable'),
            ],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.delivery' => 'string',
        ];
    }
}
