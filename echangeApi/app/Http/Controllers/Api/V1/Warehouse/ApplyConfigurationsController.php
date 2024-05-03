<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Constants\ImportsInformation;
use App\Http\Requests\Api\V1\Warehouse\ApplyConfigurationRequest;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Carbon\Carbon;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response as FacadesResponse;

class ApplyConfigurationsController extends JsonApiController
{
    public function applyConfigurations(Warehouse $warehouse, ApplyConfigurationRequest $request): Response
    {
        $modelWarehouseTaxGroups = $warehouse->taxGroups->pluck('id');
        $results = [];
        $modelWarehouseProductsIds = $warehouse->warehouseProducts->pluck('product_id');
        Log::info($modelWarehouseProductsIds);

        DB::beginTransaction();
        try {
            /** @var Warehouse $subWarehouse */
            foreach ($warehouse->usedBy as $subWarehouse) {
                $subWarehouse->organization_id = $warehouse->organization_id;
                $subWarehouse->allow_unregistered_products = $warehouse->allow_unregistered_products;
                $subWarehouse->allow_negative_inventory = $warehouse->allow_negative_inventory;
                $subWarehouse->use_warehouse_taxes = $warehouse->use_warehouse_taxes;
                $subWarehouse->applied_at = Carbon::now();
                $subWarehouse->save();
                $subWarehouse->taxGroups()->sync($modelWarehouseTaxGroups);

                $subWarehouseSyncResults = [
                    ImportsInformation::STATUS_CREATED => 0,
                    ImportsInformation::STATUS_UPDATED => 0,
                    ImportsInformation::STATUS_DELETED => 0,
                    ImportsInformation::ERRORS => [],
                ];

                /** @var WarehouseProduct $modelWarehouseProduct */
                foreach ($warehouse->warehouseProducts as $modelWarehouseProduct) {
                    $subWarehouseProduct = WarehouseProduct::where('warehouse_id', $subWarehouse->id)
                        ->where('product_id', $modelWarehouseProduct->product_id)
                        ->first();

                    try {
                        if ($subWarehouseProduct == null) {
                            $subWarehouseProduct = new WarehouseProduct();
                            $subWarehouseProduct->quantity = 0;
                            $subWarehouseProduct->product_id = $modelWarehouseProduct->product_id;
                            $subWarehouseProduct->warehouse()->associate($subWarehouse);
                            $subWarehouseSyncResults[ImportsInformation::STATUS_CREATED] = $subWarehouseSyncResults[ImportsInformation::STATUS_CREATED] + 1;
                        } else {
                            $subWarehouseSyncResults[ImportsInformation::STATUS_UPDATED] = $subWarehouseSyncResults[ImportsInformation::STATUS_UPDATED] + 1;
                        }
                        $subWarehouseProduct->selling_price = $modelWarehouseProduct->selling_price;
                        $subWarehouseProduct->buying_price = $modelWarehouseProduct->buying_price;
                        $subWarehouseProduct->sku = $modelWarehouseProduct->sku;
                        $subWarehouseProduct->custom_pricing = $modelWarehouseProduct->custom_pricing;
                        $subWarehouseProduct->custom_taxation = $modelWarehouseProduct->custom_taxation;
                        $subWarehouseProduct->save();
                    } catch (\Exception $e) {
                        $subWarehouseSyncResults[ImportsInformation::ERRORS][] = [
                            'warehouseProductId' => $subWarehouseProduct->id,
                            'modelWarehouseProduct' => [
                                'id' => $modelWarehouseProduct->id,
                                'name' => $modelWarehouseProduct->getName(),
                            ],
                            'message' => $e->getMessage(),
                        ];
                    }
                }

                $warehouseProductsToDelete = WarehouseProduct::where('warehouse_id', $subWarehouse->id)
                    ->whereNotIn('product_id', $modelWarehouseProductsIds)
                    ->get();

                foreach ($warehouseProductsToDelete as $warehouseProduct) {
                    if ($warehouseProduct->quantity != 0) {
                        $subWarehouseSyncResults[ImportsInformation::ERRORS][] = [
                            'product' => [
                                'id' => $warehouseProduct->id,
                                'name' => $warehouseProduct->getName(),
                            ],
                            'message' => __(
                                'errors.product_x_quantity_is_not_null',
                                [
                                    'product' => $warehouseProduct->getName(),
                                    'quantity' => $warehouseProduct->quantity,
                                ]
                            ),
                        ];

                        continue;
                    }

                    try {
                        $warehouseProduct->delete();
                        $subWarehouseSyncResults[ImportsInformation::STATUS_DELETED] = $subWarehouseSyncResults[ImportsInformation::STATUS_DELETED] + 1;
                    } catch (\Exception $e) {
                        $subWarehouseSyncResults[ImportsInformation::ERRORS][] = [
                            'warehouseProduct' => [
                                'id' => $warehouseProduct->id,
                                'name' => $warehouseProduct->getName(),
                            ],
                            'message' => $e->getMessage(),
                        ];
                    }
                }

                $results[] = [
                    'results' => $subWarehouseSyncResults,
                    'id' => $subWarehouse->id,
                    'name' => $subWarehouse->name,
                ];
            }

            DB::commit();

            return FacadesResponse::make(['data' => $results], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Transaction failed',
                    'detail' => $e->getMessage(),
                    'status' => '500',
                ]),
            ]);
        }
    }
}
