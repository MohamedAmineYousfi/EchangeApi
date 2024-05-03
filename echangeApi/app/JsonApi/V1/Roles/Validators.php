<?php

namespace App\JsonApi\V1\Roles;

use App\Constants\Permissions;
use App\Models\User;
use App\Rules\AllowedLocations;
use CloudCreativity\LaravelJsonApi\Rules\AllowedIncludePaths;
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
        'permissions',
        'organization',
        'reseller',
        'allowedLocations',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = ['name', 'created_at'];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'name',
        'organization',
        'reseller',
        'allowedLocations',
        'id',
        'ids',
    ];

    /**
     * Get a rule for the allowed include paths.
     * Overwrite to allow setting include paths based on permissions
     */
    protected function allowedIncludePaths(): AllowedIncludePaths
    {
        /** @var ?User $user */
        $user = auth()->user();
        if ($user->can(Permissions::PERM_VIEW_ANY_USERS)) {
            $this->allowedIncludePaths[] = 'users';
        }
        $this->allowedIncludePaths[] = 'permissions';

        return new AllowedIncludePaths($this->allowedIncludePaths);
    }

    /**
     * Get resource validation rules.
     *
     * @param  mixed|null  $record
     *                              the record being updated, or null if creating a resource.
     */
    protected function rules($record, array $data): array
    {
        return [
            'name' => 'required|string',
            'excerpt' => 'nullable|string',
            'organization' => ['sometimes', new HasOne('organizations')],
            'reseller' => ['sometimes', new HasOne('resellers')],
            'permissions' => ['required', new HasMany('permissions')],
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
            'filter.name' => 'filled|string',
            'filter.organization' => 'filled|string',
            'filter.reseller' => 'filled|string',
        ];
    }
}
