<?php

namespace App\JsonApi\V1\Imports;

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
    protected $allowedIncludePaths = ['organization', 'importedItems', 'linkedObject'];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'created_at',
        'updated_at',
        'name',
        'organization',
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
        'id',
        'linkedObjectType',
        'linkedObjectId',
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
            'model' => ['required', 'string'],
            'name' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'mapping' => ['required', 'array'],
            'file_url' => ['required', 'url'],
            'organization' => ['nullable', new HasOne('organizations')],
            'linked_object' => [
                new HasOne('suppliers'),
            ],
            'identifier.csv_field' => ['required', 'string'],
            'identifier.model_field' => ['required', 'string'],
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
            'filter.organization' => 'filled|string',
            'filter.search' => 'filled|string',
            'filter.name' => 'filled|string',
        ];
    }
}
