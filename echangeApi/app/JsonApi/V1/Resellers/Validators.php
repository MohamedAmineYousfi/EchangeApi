<?php

namespace App\JsonApi\V1\Resellers;

use App\Rules\PhoneNumber;
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
    protected $allowedIncludePaths = ['owner'];

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
        'id',
        'name',
        'owner',
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
            'name' => ['required', 'string', 'min:3', 'max:128'],
            'excerpt' => ['nullable', 'string'],
            'email' => [
                'required',
                'email',
                Rule::unique('resellers')->ignore($record ? $record->id : null),
            ],
            'address' => ['required', 'string', 'min:3', 'max:254'],
            'phone' => [
                'required',
                'string',
                Rule::unique('resellers')->ignore($record ? $record->id : null),
                new PhoneNumber(),
            ],
            'phone_extension' => ['nullable', 'sometimes', 'string'],
            'phone_type' => ['nullable', 'sometimes', 'string'],
            'other_phones' => ['nullable', 'array'],
            'other_phones.phoneNumber' => ['string', 'distinct', new PhoneNumber()],
            'other_phones.extension' => ['string'],
            'logo' => ['nullable', 'url'],
            'config_manager_app_name' => ['nullable', 'string'],
            'config_manager_app_logo' => ['nullable', 'string'],
            'config_manager_url_regex' => ['nullable', 'string'],
            'owner' => ['required', new HasOne('users')],
        ];
    }

    /**
     * Get query parameter validation rules.
     */
    protected function queryRules(): array
    {
        return [
            'filter.name' => 'filled|string',
            'filter.reseller' => 'filled|string',
            'filter.owner' => 'filled|string',
        ];
    }
}
