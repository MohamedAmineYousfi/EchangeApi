<?php

namespace App\Helpers;

use App\Helpers\UnitOfMeasure as UnitOfMeasureHelper;
use App\Models\StockMovement as ModelsStockMovement;
use App\Models\StockMovementItem as ModelsStockMovementItem;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Error;

class StockMovement
{
    public static function validateSourceWarehouse(
        Warehouse $sourceWarehouse,
        ModelsStockMovementItem $item,
        float $quantity
    ): void {
        $warehouseProduct = $sourceWarehouse
            ->warehouseProducts()
            ->where('product_id', '=', $item->storable->id)
            ->first();

        if (! $sourceWarehouse->allow_unregistered_products) {
            if (! $warehouseProduct) {
                throw new Error(
                    __('errors.product_not_allowed_for_warehouse_x', [
                        'warehouse' => $sourceWarehouse->name,
                    ])
                );
            }
        }

        $unitQuantity = $quantity;
        $unit = null;
        if ($item->storable->unitOfMeasure) {
            $unitQuantity = UnitOfMeasureHelper::convertQuantityToUnit($quantity, $item->unitOfMeasureUnit, $warehouseProduct->unitOfMeasureUnit);
            $unit = $item->unitOfMeasureUnit->name;
        }

        if ($warehouseProduct->quantity < $unitQuantity) {
            if (! $sourceWarehouse->allow_negative_inventory) {
                throw new Error(
                    __('errors.quantity_remainin_in_warehouse_x_is_less_than_x_x', [
                        'warehouse' => $sourceWarehouse->name,
                        'quantity' => $quantity,
                        'unit' => $unit,
                    ])
                );
            }
        }
    }

    public static function validateDestinationWarehouse(
        Warehouse $destinationWarehouse,
        ModelsStockMovementItem $item
    ): void {
        $warehouseProduct = $destinationWarehouse
            ->warehouseProducts()
            ->where('product_id', '=', $item->storable->id)
            ->first();

        if (! $destinationWarehouse->allow_unregistered_products) {
            if (! $warehouseProduct) {
                throw new Error(
                    __('errors.product_not_allowed_for_warehouse_x', [
                        'warehouse' => $destinationWarehouse->name,
                    ])
                );
            }
        }
    }

    public static function validateStockMovement(ModelsStockMovement $stockMovement): void
    {
        /** @var ModelsStockMovementItem $item */
        foreach ($stockMovement->items as $item) {

            if ($stockMovement->movement_type === ModelsStockMovement::TYPE_ADD) {
                /** @var Warehouse */
                $destinationWarehouse = $stockMovement->destinationWarehouse;
                self::validateDestinationWarehouse($destinationWarehouse, $item);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_REMOVE) {
                /** @var Warehouse */
                $sourceWarehouse = $stockMovement->sourceWarehouse;
                self::validateSourceWarehouse($sourceWarehouse, $item, $item->quantity);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_MOVE) {
                /** @var Warehouse */
                $sourceWarehouse = $stockMovement->sourceWarehouse;
                self::validateSourceWarehouse($sourceWarehouse, $item, $item->quantity);

                /** @var Warehouse */
                $destinationWarehouse = $stockMovement->destinationWarehouse;
                self::validateDestinationWarehouse($destinationWarehouse, $item);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_CORRECT) {
            }
        }
    }

    public static function applyStockMovement(ModelsStockMovement $stockMovement): void
    {
        /** @var ModelsStockMovementItem $item */
        foreach ($stockMovement->items as $item) {

            if ($stockMovement->movement_type === ModelsStockMovement::TYPE_ADD) {
                /** @var Warehouse */
                $destinationWarehouse = $stockMovement->destinationWarehouse;
                self::addProductWareHouse($destinationWarehouse, $item, $item->quantity);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_REMOVE) {
                /** @var Warehouse */
                $sourceWarehouse = $stockMovement->sourceWarehouse;
                self::removeProductWareHouse($sourceWarehouse, $item, $item->quantity);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_MOVE) {
                /** @var Warehouse */
                $sourceWarehouse = $stockMovement->sourceWarehouse;
                self::removeProductWareHouse($sourceWarehouse, $item, $item->quantity);

                /** @var Warehouse */
                $destinationWarehouse = $stockMovement->destinationWarehouse;
                self::addProductWareHouse($destinationWarehouse, $item, $item->quantity);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_CORRECT) {
                /** @var Warehouse */
                $sourceWarehouse = $stockMovement->sourceWarehouse;
                self::setProductWareHouse($sourceWarehouse, $item, $item->quantity);
            }
        }
    }

    public static function revertStockMovement(ModelsStockMovement $stockMovement): void
    {
        /** @var ModelsStockMovementItem */
        foreach ($stockMovement->items as $item) {

            if ($stockMovement->movement_type === ModelsStockMovement::TYPE_ADD) {
                /** @var Warehouse */
                $destinationWarehouse = $stockMovement->destinationWarehouse;
                self::removeProductWareHouse($destinationWarehouse, $item, $item->quantity);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_REMOVE) {
                /** @var Warehouse */
                $sourceWarehouse = $stockMovement->sourceWarehouse;
                self::addProductWareHouse($sourceWarehouse, $item, $item->quantity);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_MOVE) {
                /** @var Warehouse */
                $sourceWarehouse = $stockMovement->sourceWarehouse;
                self::addProductWareHouse($sourceWarehouse, $item, $item->quantity);

                /** @var Warehouse */
                $destinationWarehouse = $stockMovement->destinationWarehouse;
                self::removeProductWareHouse($destinationWarehouse, $item, $item->quantity);
            } elseif ($stockMovement->movement_type === ModelsStockMovement::TYPE_CORRECT) {
            }
        }
    }

    public static function addProductWareHouse(Warehouse $warehouse, ModelsStockMovementItem $item, float $quantity)
    {
        /** @var WarehouseProduct $warehouseProduct */
        $warehouseProduct = $warehouse->warehouseProducts()->where('product_id', '=', $item->storable->id)->first();

        if ($warehouseProduct == null) {
            $warehouseProduct = new WarehouseProduct();
            $warehouseProduct->quantity = 0;
            $warehouseProduct->selling_price = $item->storable->selling_price;
            $warehouseProduct->buying_price = $item->storable->buying_price;
            $warehouseProduct->warehouse()->associate($warehouse);
            $warehouseProduct->product()->associate($item->storable);
        }

        $unitQuantity = $quantity;
        if ($item->storable->unitOfMeasure) {
            $unitQuantity = UnitOfMeasureHelper::convertQuantityToUnit($quantity, $item->unitOfMeasureUnit, $warehouseProduct->unitOfMeasureUnit);
        }

        $warehouseProduct->quantity = $warehouseProduct->quantity + $unitQuantity;
        $warehouseProduct->save();
    }

    public static function removeProductWareHouse(Warehouse $warehouse, ModelsStockMovementItem $item, float $quantity)
    {
        /** @var WarehouseProduct $warehouseProduct */
        $warehouseProduct = $warehouse->warehouseProducts()->where('product_id', '=', $item->storable->id)->first();

        if ($warehouseProduct == null) {
            /** @var WarehouseProduct $warehouseProduct */
            $warehouseProduct = new WarehouseProduct();
            $warehouseProduct->quantity = 0;
            $warehouseProduct->selling_price = $item->storable->selling_price;
            $warehouseProduct->buying_price = $item->storable->buying_price;
            $warehouseProduct->warehouse()->associate($warehouse);
            $warehouseProduct->product()->associate($item->storable);
        }

        $unitQuantity = $quantity;
        if ($item->storable->unitOfMeasure) {
            $unitQuantity = UnitOfMeasureHelper::convertQuantityToUnit($quantity, $item->unitOfMeasureUnit, $warehouseProduct->unitOfMeasureUnit);
        }

        $warehouseProduct->quantity = $warehouseProduct->quantity - $unitQuantity;
        $warehouseProduct->save();
    }

    public static function setProductWareHouse(Warehouse $warehouse, ModelsStockMovementItem $item, float $quantity)
    {
        /** @var WarehouseProduct $warehouseProduct */
        $warehouseProduct = $warehouse->warehouseProducts()->where('product_id', '=', $item->storable->id)->first();

        if ($warehouseProduct == null) {
            $warehouseProduct = new WarehouseProduct();
            $warehouseProduct->quantity = 0;
            $warehouseProduct->selling_price = $item->storable->selling_price;
            $warehouseProduct->buying_price = $item->storable->buying_price;
            $warehouseProduct->warehouse()->associate($warehouse);
            $warehouseProduct->product()->associate($item->storable);
        }

        $unitQuantity = $quantity;
        if ($item->storable->unitOfMeasure) {
            $unitQuantity = UnitOfMeasureHelper::convertQuantityToUnit($quantity, $item->unitOfMeasureUnit, $warehouseProduct->unitOfMeasureUnit);
        }

        $warehouseProduct->quantity = $unitQuantity;
        $warehouseProduct->save();
    }
}
