<?php

namespace App\Rules;

use App\Helpers\StockMovement as HelpersStockMovement;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockMovementItem as ModelsStockMovementItem;
use App\Models\Warehouse;
use Error;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

/**
 * {@inheritDoc}
 *
 * @property Product $storable
 */
class StockMovementItem implements DataAwareRule, Rule
{
    private $data = null;

    private $message;

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->data['quantity'] == 0) {
            return true;
        }

        /** @var StockMovement|null */
        $stockMovement = StockMovement::find($this->data['stockMovement']['id']);
        if (! $stockMovement) {
            $this->message = 'Stock movement not found';

            return false;
        }

        $stockMovementItem = ModelsStockMovementItem::find($this->data['id']);

        if ($stockMovement->movement_type === StockMovement::TYPE_ADD) {
            /** @var Warehouse */
            $destinationWarehouse = $stockMovement->destinationWarehouse;
            try {
                HelpersStockMovement::validateDestinationWarehouse($destinationWarehouse, $stockMovementItem);
            } catch (Error $e) {
                $this->message = $e->getMessage();

                return false;
            }
        } elseif ($stockMovement->movement_type === StockMovement::TYPE_REMOVE) {
            /** @var Warehouse */
            $sourceWarehouse = $stockMovement->sourceWarehouse;
            try {
                HelpersStockMovement::validateSourceWarehouse($sourceWarehouse, $stockMovementItem, $this->data['quantity']);
            } catch (Error $e) {
                $this->message = $e->getMessage();

                return false;
            }
        } elseif ($stockMovement->movement_type === StockMovement::TYPE_MOVE) {
            /** @var Warehouse */
            $sourceWarehouse = $stockMovement->sourceWarehouse;
            try {
                HelpersStockMovement::validateSourceWarehouse($sourceWarehouse, $stockMovementItem, $this->data['quantity']);
            } catch (Error $e) {
                $this->message = $e->getMessage();

                return false;
            }
            /** @var Warehouse */
            $destinationWarehouse = $stockMovement->destinationWarehouse;
            try {
                HelpersStockMovement::validateDestinationWarehouse($destinationWarehouse, $stockMovementItem);
            } catch (Error $e) {
                $this->message = $e->getMessage();

                return false;
            }
        } elseif ($stockMovement->movement_type === StockMovement::TYPE_CORRECT) {
            return true;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return $this->message;
    }
}
