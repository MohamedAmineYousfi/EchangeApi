<?php

namespace App\JsonApi\V1\PurchasesOrders;

use App\Models\PurchasesOrder;
use App\Rules\AllowedLocations;
use CloudCreativity\LaravelJsonApi\Rules\DateTimeIso8601;
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
        'items',
        'items.purchasesOrderable',
        'user',
        'issuer',
        'payments',
        'organization',
        'allowedLocations',
        'destinationWarehouse',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'code',
        'expiration_time',
        'created_at',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'code',
        'created_at',
        'status',
        'issuer_type',
        'issuer_id',
        'organization',
        'id',
        'allowedLocations',
        'invoicing_status',
        'delivery_status',
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
            if ($record->status != PurchasesOrder::STATUS_DRAFT) {
                abort(400, 'CANNOT_UPDATE_ORDER_NOT_DRAFT');
            }
        }

        return [
            'expiration_time' => ['required', new DateTimeIso8601()],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'has_no_taxes' => ['sometimes', 'nullable', 'boolean'],
            'issuer' => [
                'required',
                new HasOne('suppliers'),
            ],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
            'allowedLocations' => [
                new AllowedLocations(),
                new HasMany('locations'),
            ],
            'destinationWarehouse' => [
                'sometimes',
                'nullable',
                new HasOne('warehouses'),
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
            'filter.code' => 'string',
            'filter.recipient' => 'string',
            'filter.status' => 'string',
            'filter.id' => 'string',
        ];
    }
}
