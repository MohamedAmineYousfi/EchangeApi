<?php

namespace App\Constants;

class DeliveryInformations
{
    const TYPE_INDIVIDUAL = 'INDIVIDUAL';

    const TYPE_COMPANY = 'COMPANY';

    const MODEL_DELIVERY_INFORMATIONS_FILLABLES = [
        'delivery_entity_type',
        'delivery_company_name',
        'delivery_firstname',
        'delivery_lastname',
        'delivery_country',
        'delivery_state',
        'delivery_city',
        'delivery_zipcode',
        'delivery_address',
        'delivery_email',
    ];

    const DELIVERY_INFORMATIONS_FORM_RULES = [
        'delivery_entity_type' => [
            'required',
            'in:'.DeliveryInformations::TYPE_INDIVIDUAL.','.DeliveryInformations::TYPE_COMPANY,
        ],
        'delivery_company_name' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_firstname' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_lastname' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_country' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_state' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_city' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_zipcode' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_address' => ['required', 'string', 'min:0', 'max:128'],
        'delivery_email' => ['required', 'email'],
    ];
}
