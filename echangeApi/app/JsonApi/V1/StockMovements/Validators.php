<?php

namespace App\JsonApi\V1\StockMovements;

use App\Models\StockMovement;
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
        'sourceWarehouse',
        'destinationWarehouse',
        'items',
        'items.storable',
        'items.storable.unitOfMeasure',
        'items.unitOfMeasureUnit',
        'organization',
        'allowedLocations',
        'destinationWarehouse',
        'triggerObject',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
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
        'type',
        'id',
        'ids',
        'source_warehouse',
        'destination_warehouse',
        'warehouse',
        'trigger_object',
        'organization',
        'allowedLocations',
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
            if ($record->status != StockMovement::STATUS_DRAFT) {
                abort(400, 'CANNOT_UPDATE_STOCK_MOVEMENT_NOT_DRAFT');
            }
        }

        return [
            'movement_type' => [
                'required', 'in:'.StockMovement::TYPE_ADD.','.StockMovement::TYPE_REMOVE.','.StockMovement::TYPE_MOVE.','.StockMovement::TYPE_CORRECT,
            ],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'sourceWarehouse' => [
                Rule::requiredIf(function () {
                    return in_array(
                        request()->post('data')['attributes']['movement_type'],
                        [StockMovement::TYPE_REMOVE, StockMovement::TYPE_MOVE, StockMovement::TYPE_CORRECT]
                    );
                }),
                new HasOne('warehouses'),
            ],
            'destinationWarehouse' => [
                Rule::requiredIf(function () {
                    return in_array(
                        request()->post('data')['attributes']['movement_type'],
                        [StockMovement::TYPE_ADD, StockMovement::TYPE_MOVE]
                    );
                }),
                new HasOne('warehouses'),
            ],
            'triggerObject' => [
                'sometimes',
                'nullable',
                new HasOne('sales-deliveries', 'purchases-deliveries'),
            ],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
            'allowedLocations' => [
                new AllowedLocations(),
                new HasMany('locations'),
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
