<?php

namespace App\JsonApi\V1\Warehouses;

use App\Rules\AllowedLocations;
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
        'organization',
        'allowedLocations',
        'usedBy',
        'modelUsed',
        'taxGroups',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'name',
        'created_at',
        'updated_at',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'organization',
        'name',
        'search',
        'id',
        'allowedLocations',
        'ids',
        'isModel',
        'idsNotIn',
        'modelUsed',
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
            'name' => ['required', 'string'],
            'is_model' => ['sometimes', 'boolean'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'allow_negative_inventory' => ['required', 'boolean'],
            'allow_unregistered_products' => ['required', 'boolean'],
            'use_warehouse_taxes' => ['required', 'boolean'],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
            'modelUsed' => [
                'sometimes',
                'nullable',
                new HasOne('warehouses'),
            ],
            'usedBy' => [
                new HasMany('warehouses'),
            ],
            'allowedLocations' => [
                new AllowedLocations(),
                new HasMany('locations'),
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
            'filter.organization' => 'string',
            'filter.name' => 'string',
            'filter.id' => 'string',
            'filter.ids' => 'array',
        ];
    }
}
