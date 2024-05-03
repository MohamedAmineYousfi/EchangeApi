<?php

namespace App\Support\Traits;

use App\Constants\DeliveryInformations;
use App\Support\Interfaces\ModelIsDeliverableTo;

trait ModelHasDeliveryInformations
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function (ModelIsDeliverableTo $model) {
            if ($model->delivery_entity_type === DeliveryInformations::TYPE_INDIVIDUAL) {
                $model->delivery_company_name = 'N/A';
            } elseif ($model->delivery_entity_type === DeliveryInformations::TYPE_COMPANY) {
                $model->delivery_firstname = 'N/A';
                $model->delivery_lastname = 'N/A';
            }
        });
    }

    /**
     * getDeliveryInformations
     */
    public function getDeliveryInformations(): array
    {
        return [
            'delivery_entity_type' => $this->delivery_entity_type,
            'delivery_company_name' => $this->delivery_company_name ?? 'NOT_DEFINED',
            'delivery_firstname' => $this->delivery_firstname ?? 'NOT_DEFINED',
            'delivery_lastname' => $this->delivery_lastname ?? 'NOT_DEFINED',
            'delivery_country' => $this->delivery_country ?? 'NOT_DEFINED',
            'delivery_state' => $this->delivery_state ?? 'NOT_DEFINED',
            'delivery_city' => $this->delivery_city ?? 'NOT_DEFINED',
            'delivery_zipcode' => $this->delivery_zipcode ?? 'NOT_DEFINED',
            'delivery_address' => $this->delivery_address ?? 'NOT_DEFINED',
            'delivery_email' => $this->delivery_email ?? 'NOT_DEFINED',
        ];
    }
}
