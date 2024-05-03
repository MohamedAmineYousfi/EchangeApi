<?php

namespace App\JsonApi\V1\Packages;

use App\Rules\StringTimeInterval;
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
    protected $allowedIncludePaths = ['reseller', 'default_role', 'default_role.permissions', 'taxGroups'];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = ['created_at', 'name', 'price', 'code'];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = ['search', 'organization', 'reseller', 'id', 'ids'];

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
            'name' => ['required', 'string', 'min:3', 'max:128'],
            'excerpt' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'picture' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['url', 'distinct'],
            'frequency' => ['required', new StringTimeInterval()],
            'maximum_users' => ['required', 'numeric'],
            'reseller' => ['required', new HasOne('resellers')],
            'default_role' => ['required', new HasOne('roles')],
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
            'filter.search' => 'filled|string',
            'filter.reseller' => 'filled|string',
        ];
    }
}
