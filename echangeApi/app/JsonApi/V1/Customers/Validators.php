<?php

namespace App\JsonApi\V1\Customers;

use App\Constants\BillingInformations;
use App\Models\Customer;
use App\Rules\AllowedLocations;
use App\Rules\PhoneNumber;
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
        'user',
        'organization',
        'contacts',
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
        return [
            'customer_type' => ['required', 'in:'.Customer::CUSTOMER_TYPE_INDIVIDUAL.','.Customer::CUSTOMER_TYPE_COMPANY],
            'company_name' => ['required_if:customer_type,'.Customer::CUSTOMER_TYPE_COMPANY, 'string', 'min:3', 'max:128'],
            'firstname' => ['required_if:customer_type,'.Customer::CUSTOMER_TYPE_INDIVIDUAL, 'string', 'min:3', 'max:128'],
            'lastname' => ['required_if:customer_type,'.Customer::CUSTOMER_TYPE_INDIVIDUAL, 'string', 'min:3', 'max:128'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', new PhoneNumber()],
            'phone_extension' => ['nullable', 'sometimes', 'string'],
            'phone_type' => ['nullable', 'sometimes', 'string'],
            'other_phones' => ['nullable', 'array'],
            'other_phones.phoneNumber' => ['string', 'distinct', new PhoneNumber()],
            'other_phones.extension' => ['string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
            'zipcode' => ['required', 'string'],
            'address' => ['required', 'string'],
            'organization' => [
                'required',
                new HasOne('organizations'),
            ],
            'recipient' => [
                new HasOne('customers', 'users'),
            ],
            'contacts' => [
                new HasMany('contacts'),
            ],
            'tags' => [new HasMany('tags')],
            'allowedLocations' => [
                new AllowedLocations(),
                new HasMany('locations'),
            ],
            ...BillingInformations::BILLING_INFORMATIONS_FORM_RULES,
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
