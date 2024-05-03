<?php

namespace App\JsonApi\V1\Auctions;

use App\Constants\AuctionInformation;
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
        'allowedLocations',
        'managers',
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
        'name',
        'end_at',
        'start_at',
        'object_type',
        'auction_type',
    ];

    /**
     * The filters a client is allowed send.
     **
     * @var string[]|null
     *                    the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [
        'organization',
        'allowedLocations',
        'managers',
        'search',
        'end_at',
        'start_at',
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
            'name' => ['required', 'min:3', 'max:128'],
            'auction_type' => ['required', 'string', 'in:'.implode(',', AuctionInformation::AUCTION_TYPES)],
            'object_type' => ['required', 'string', 'in:'.implode(',', AuctionInformation::OBJECT_TYPES)],
            'authorized_payments' => ['required', 'array'],
            'authorized_payments.*' => ['required', 'string', 'in:'.implode(',', AuctionInformation::AUCTION_PAYMENTS)],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date'],
            'listings_registrations_close_at' => ['required', 'date'],
            'listings_registrations_open_at' => ['required', 'date'],
            'pre_opening_at' => ['nullable', 'date'],
            'activated_timer' => ['sometimes', 'boolean'],
            'extension_time' => ['nullable', 'integer', 'min:0'],
            'delay' => ['required', 'integer', 'min:0'],
            'excerpt' => ['nullable', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
            'zipcode' => ['sometimes', 'nullable', 'string'],
            'address' => ['required', 'string'],
            'lat' => ['numeric', 'between:-90,90', 'nullable'],
            'long' => ['numeric', 'between:-180,180', 'nullable'],
            'organization' => ['required', new HasOne('organizations')],
            'allowedLocations' => [
                'required',
                new AllowedLocations(),
                new HasMany('locations'),
            ],
            'managers' => [
                'required',
                new HasMany('users'),
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
            'filter.organization' => 'filled|string',
        ];
    }
}
