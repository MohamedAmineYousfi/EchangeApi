<?php

namespace App\JsonApi\V1\ResellerInvoiceItems;

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
        'invoice',
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
            'resellerInvoice' => [
                'required',
                new HasOne('reseller-invoices'),
            ],
            'invoiceable' => [
                new HasOne('reseller-products', 'reseller-services', 'packages'),
            ],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.invoice' => 'string',
        ];
    }
}
