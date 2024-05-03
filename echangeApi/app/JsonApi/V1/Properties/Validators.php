<?php

namespace App\JsonApi\V1\Properties;

use App\Constants\AuctionInformation;
use App\Constants\Permissions;
use App\Constants\PropertyInformation;
use App\Constants\PropertyTransactionInformation;
use App\Rules\AllowedLocations;
use App\Rules\UniqueRegistrationNumber;
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
        'owners',
        'auction',
        'updatedBy',
        'createdBy',
        'organization',
        'allowedLocations',
        'paymentReceivedBy',
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [
        'created_at',
        'updated_at',
        'designation',
        'sold_amount',
        'sold_at',
        'property_number',
        'status',
    ];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'organization',
        'auctionId',
        'allowedLocations',
        'owners',
        'status',
        'search',
        'active',
        'owner',
        'onlyConfirmed',
    ];

    /**
     * Get resource validation rules.
     *
     * @param mixed|null $record
     *                              the record being updated, or null if creating a resource.
     * @param array $data
     *                       the data being validated
     */
    protected function rules($record, array $data): array
    {
        $user = auth()->user();
        $allPropertiesFields = [];
        $attributes = [];
        if (isset($data['attributes'])) {
            $attributes = $data['attributes'];
        }
        if ($user->can(Permissions::PERM_ACCESS_ALL_FIELDS_PROPERTIES)) {
            $allPropertiesFields = [
                'status' => ['required', 'string', 'in:' . implode(',', PropertyInformation::STATUS)],
                'sold_at' => ['nullable', 'date'],
                'cancel_reason' => ['nullable', 'string',
                    Rule::requiredIf(function () use ($attributes) {
                        return $attributes['status'] == PropertyInformation::STATUS_CANCEL;
                    })],
                'sold_amount' => ['nullable', 'numeric'],
                'taxable' => ['sometimes', 'boolean'],
                'mrc_fees' => ['nullable', 'numeric'],
                'property_number' => ['nullable', 'sometimes', 'min:3', 'max:254'],
            ];
        }

        return [
            'designation' => ['required', 'string', 'min:3'],
            'acquisition_number' => ['required', 'min:3', 'max:128'],
            'acquisition_method' => ['required', 'min:3', 'max:200'],
            'owed_taxes_school_board' => ['nullable', 'numeric'],
            'owed_taxes_municipality' => ['nullable', 'numeric'],
            'batch_numbers' => ['required', 'array'],
            'batch_numbers.*.value' => ['required', 'numeric', 'digits:7'],
            'registration_number' => [
                new UniqueRegistrationNumber($record ? $record->id : null),
                'required', 'string', 'min:3', 'max:128',
            ],
            'status' => ['required', 'string', 'in:' . implode(',', PropertyInformation::STATUS)],
            'property_type' => ['required', 'string', 'in:' . implode(',', PropertyInformation::TYPES)],
            'excerpt' => ['nullable', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
            'zipcode' => ['sometimes', 'nullable', 'string'],
            'address' => ['required', 'string'],
            ...$allPropertiesFields,
            'organization' => ['required', new HasOne('organizations')],
            'auction' => ['nullable', new HasOne('auctions')],
            'allowedLocations' => [
                'required',
                new AllowedLocations(),
                new HasMany('locations'),
            ],
            'owners' => [
                'required',
                new HasMany('contacts'),
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
            'filter.status' => 'filled|string',
            'filter.organization' => 'filled|string',
        ];
    }
}
