<?php

namespace App\Constants;

class BillingInformations
{
    const TYPE_INDIVIDUAL = 'INDIVIDUAL';

    const TYPE_COMPANY = 'COMPANY';

    const MODEL_BILLING_INFORMATIONS_FILLABLES = [
        'billing_entity_type',
        'billing_company_name',
        'billing_firstname',
        'billing_lastname',
        'billing_country',
        'billing_state',
        'billing_city',
        'billing_zipcode',
        'billing_address',
        'billing_email',
    ];

    const BILLING_INFORMATIONS_FORM_RULES = [
        'billing_entity_type' => [
            'required',
            'in:'.BillingInformations::TYPE_INDIVIDUAL.','.BillingInformations::TYPE_COMPANY,
        ],
        'billing_company_name' => ['required', 'string', 'min:0', 'max:128'],
        'billing_firstname' => ['required', 'string', 'min:0', 'max:128'],
        'billing_lastname' => ['required', 'string', 'min:0', 'max:128'],
        'billing_country' => ['required', 'string', 'min:0', 'max:128'],
        'billing_state' => ['required', 'string', 'min:0', 'max:128'],
        'billing_city' => ['required', 'string', 'min:0', 'max:128'],
        'billing_zipcode' => ['required', 'string', 'min:0', 'max:128'],
        'billing_address' => ['required', 'string', 'min:0', 'max:128'],
        'billing_email' => ['required', 'email'],
    ];
}
