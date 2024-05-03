<?php

namespace App\JsonApi\V1\Users;

use App\Constants\BillingInformations;
use App\Rules\AllowedLocations;
use App\Rules\PhoneNumber;
use CloudCreativity\LaravelJsonApi\Rules\HasMany;
use CloudCreativity\LaravelJsonApi\Rules\HasOne;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class Validators extends AbstractValidators
{
    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *                    the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = [
        'roles', 'roles.permissions', 'organization', 'reseller', 'allowedLocations',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'firstname', 'lastname', 'email', 'roles.name', 'created_at',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'search', 'roles', 'organization', 'reseller', 'is_staff', 'id', 'allowedLocations', 'ids',
        'notLinkedToLocation',
    ];

    /**
     * Get resource validation rules.
     *
     * @param  mixed|null  $record
     *                              the record being updated, or null if creating a resource.
     */
    protected function rules($record, array $data): array
    {
        if ($record) {
            return [
                'firstname' => ['required', 'string', 'min:3', 'max:128'],
                'lastname' => ['required', 'string', 'min:3', 'max:128'],
                'email' => ['required', 'email', Rule::unique('users')->ignore($record->id)],
                'phone' => ['required', 'string', 'min:3', 'max:128', new PhoneNumber()],
                'phone_extension' => ['nullable', 'sometimes', 'string'],
                'phone_type' => ['nullable', 'sometimes', 'string'],
                'other_phones' => ['nullable', 'array'],
                'other_phones.phoneNumber' => ['string', 'distinct', new PhoneNumber()],
                'other_phones.extension' => ['string'],
                'profile_image' => 'sometimes|nullable|url',
                'locale' => ['required', 'string', 'min:2', 'max:2'],
                'is_staff' => ['required', 'boolean'],
                'password' => [
                    'sometimes',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->uncompromised(),
                ],
                'roles' => ['nullable', new HasMany('roles')],
                'organization' => ['sometimes', 'nullable', new HasOne('organizations')],
                'reseller' => ['sometimes', 'nullable', new HasOne('resellers')],
                'restrict_to_locations' => ['required', 'boolean'],
                'allowedLocations' => [
                    new AllowedLocations(),
                    new HasMany('locations'),
                ],

                ...BillingInformations::BILLING_INFORMATIONS_FORM_RULES,
                'billing_email' => ['required', 'string'],
            ];
        }

        return [
            'firstname' => ['required', 'string', 'min:3', 'max:128'],
            'lastname' => ['required', 'string', 'min:3', 'max:128'],
            'email' => ['required', 'email', Rule::unique('users')],
            'phone' => ['required', 'string', 'min:3', 'max:128'],
            'profile_image' => 'sometimes|nullable|url',
            'locale' => ['required', 'string', 'min:2', 'max:2'],
            'is_staff' => ['required', 'boolean'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
            'roles' => ['nullable', new HasMany('roles')],
            'organization' => ['sometimes', 'nullable', new HasOne('organizations')],
            'reseller' => ['sometimes', 'nullable', new HasOne('resellers')],
            'restrict_to_locations' => ['required', 'boolean'],
            'allowedLocations' => [
                new AllowedLocations(),
                new HasMany('locations'),
            ],

            ...BillingInformations::BILLING_INFORMATIONS_FORM_RULES,
            'billing_email' => ['required', 'string'],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.search' => 'filled|string',
            'filter.roles' => 'filled|string',
            'filter.organization' => 'nullable|string',
            'filter.notLinkedToLocation' => 'nullable|string',
            'filter.reseller' => 'nullable|string',
            'filter.is_staff' => 'nullable|string',
            'filter.id' => 'filled|string',
        ];
    }
}
