<?php

namespace App\JsonApi\V1\Folders;

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
        'object',
        'tags',
        'allowedLocations',
        'parent',
        'subfolders',
        'files',
        'users',
        'roles',
        'subfolders.organization',
        'files.organization',
        'owner',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'name',
        'size',
        'created_at',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'name',
        'excerpt',
        'search',
        'id',
        'tags',
        'allowedLocations',
        'organization',
        'parent',
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
            'excerpt' => ['nullable', 'string'],
            'parent' => [new HasOne('folders')],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
            'tags' => [new HasMany('tags')],
            'roles' => [new HasMany('roles')],
            'users' => [new HasMany('users')],
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
            'filter.organization' => 'string',
            'filter.search' => 'string',
            'filter.id' => 'string',
        ];
    }
}
