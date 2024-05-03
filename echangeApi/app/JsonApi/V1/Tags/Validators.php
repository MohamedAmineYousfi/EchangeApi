<?php

namespace App\JsonApi\V1\Tags;

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
    protected $allowedSortParameters = [
        'name',
        'slug',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'id',
        'ids',
        'name',
        'organization',
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
            'name' => ['string', 'min:3', 'max:128'],
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
            'filter.slug' => 'string',
        ];
    }
}
