<?php

namespace App\Support\Traits;

use App\Constants\BillingInformations;
use App\Support\Interfaces\ModelIsBillableTo;

/**
 * @property string $customer_type
 * @property string $company_name
 * @property string $firstname
 * @property string $lastname
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $zipcode
 * @property string $email
 * @property string $address
 */
trait ModelHasBillingInformations
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function (ModelIsBillableTo $model) {
            if ($model->billing_entity_type === BillingInformations::TYPE_INDIVIDUAL) {
                $model->billing_company_name = 'N/A';
            } elseif ($model->billing_entity_type === BillingInformations::TYPE_COMPANY) {
                $model->billing_firstname = 'N/A';
                $model->billing_lastname = 'N/A';
            }
        });
    }

    /**
     * getBillingInformations
     */
    public function getBillingInformations(): array
    {
        return [
            'billing_entity_type' => $this->billing_entity_type,
            'billing_company_name' => $this->billing_company_name ?? 'NOT_DEFINED',
            'billing_firstname' => $this->billing_firstname ?? 'NOT_DEFINED',
            'billing_lastname' => $this->billing_lastname ?? 'NOT_DEFINED',
            'billing_country' => $this->billing_country ?? 'NOT_DEFINED',
            'billing_state' => $this->billing_state ?? 'NOT_DEFINED',
            'billing_city' => $this->billing_city ?? 'NOT_DEFINED',
            'billing_zipcode' => $this->billing_zipcode ?? 'NOT_DEFINED',
            'billing_address' => $this->billing_address ?? 'NOT_DEFINED',
            'billing_email' => $this->billing_email ?? 'NOT_DEFINED',
        ];
    }
}
