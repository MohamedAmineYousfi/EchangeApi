<?php

namespace App\Http\Controllers\Api\V1\Warehouse;

use App\Models\Warehouse;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\Response;

class DetachWarehouseFromUsedByListController extends JsonApiController
{
    public function detachWarehouse(Warehouse $warehouse): Response
    {
        if (! isset($warehouse->modelUsed)) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Operation failed',
                    'detail' => 'This warehouse is not associated with warehouse model',
                    'status' => '400',
                ]),
            ]);
        }
        $warehouse->modelUsed()->dissociate();
        $warehouse->save();

        return $this->reply()->content($warehouse);
    }
}
