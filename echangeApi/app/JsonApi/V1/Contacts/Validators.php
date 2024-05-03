<?php

namespace App\JsonApi\V1\Contacts;

use App\Rules\AllowedLocations;
use App\Rules\PhoneNumber;
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
        'user',
        'organization',
        'contactable',
        'tags',
        'allowedLocations',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'created_at',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'country',
        'user',
        'organization',
        'created_at',
        'search',
        'id',
        'tags',
        'contactableType',
        'contactableId',
        'allowedLocations',
        'property',
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
        $attributes = [];
        if (isset($data['attributes'])) {
            $attributes = $data['attributes'];
        }

        return [
            'title' => [
                'string', 'nullable',
                Rule::requiredIf(function () use ($attributes) {
                    return empty($attributes['company_name']);
                }),
            ],
            'company_name' => ['sometimes', 'nullable', 'string', 'min:3', 'max:128'],
            'firstname' => [
                'sometimes', 'string', 'min:3', 'max:128', 'nullable',
                Rule::requiredIf(function () use ($attributes) {
                    return empty($attributes['company_name']);
                }),
            ],
            'lastname' => [
                'sometimes', 'string', 'min:3', 'max:128', 'nullable',
                Rule::requiredIf(function () use ($attributes) {
                    return empty($attributes['company_name']);
                }),
            ],
            'email' => ['sometimes', 'nullable', 'email'],
            'phone' => ['sometimes', 'nullable', 'string', new PhoneNumber()],
            'phone_extension' => ['nullable', 'sometimes', 'string'],
            'phone_type' => ['nullable', 'sometimes', 'string'],
            'other_phones' => ['nullable', 'array'],
            'other_phones.phoneNumber' => ['string', 'distinct', new PhoneNumber()],
            'other_phones.extension' => ['string'],
            'country' => ['sometimes', 'nullable', 'string'],
            'state' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'nullable', 'string'],
            'zipcode' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'birthday' => ['sometimes', 'nullable', 'date'],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
            //'tags' => [new HasMany('tags')],
            'contactable' => [
                'nullable',
                'sometimes',
                new HasOne('customers', 'suppliers'),
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
            'filter.organization' => 'string',
            'filter.search' => 'string',
            'filter.id' => 'string',
        ];
    }
}
