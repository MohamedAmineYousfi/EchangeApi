<?php

namespace App\JsonApi\V1\SalesDeliveries;

use App\Constants\DeliveryInformations;
use App\Models\SalesDelivery;
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
        'items.salesDeliverable',
        'user',
        'recipient',
        'payments',
        'organization',
        'allowedLocations',
        'salesOrder',
        'sourceWarehouse',
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
        'recipient_type',
        'recipient_id',
        'organization',
        'id',
        'allowedLocations',
        'salesOrder',
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
            if ($record->status != SalesDelivery::STATUS_DRAFT) {
                abort(400, 'CANNOT_UPDATE_DELIVERY_NOT_DRAFT');
            }
        }

        return [
            'expiration_time' => ['required', new DateTimeIso8601()],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'recipient' => [
                'required',
                new HasOne('customers'),
            ],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
            'salesOrder' => [
                'required',
                new HasOne('sales-orders'),
            ],
            'allowedLocations' => [
                new AllowedLocations(),
                new HasMany('locations'),
            ],
            'sourceWarehouse' => [
                'sometimes',
                'nullable',
                new HasOne('warehouses'),
            ],

            ...DeliveryInformations::DELIVERY_INFORMATIONS_FORM_RULES,
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
