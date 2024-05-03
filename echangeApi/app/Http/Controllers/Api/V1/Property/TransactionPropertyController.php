<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Constants\Permissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Property\TransactionPropertyFormRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionPropertyController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param TransactionPropertyFormRequest $request
     * @param Property $property
     * @return JsonResponse
     */
    public function update(TransactionPropertyFormRequest $request, Property $property): JsonResponse
    {
        $data = $request->validated();
        $updated = $property->update($data);

        if (!$updated) {
            return response()->json([
                "error" => "An error occurred while editing the employee. Please try again later or contact the administrator.",
                "success" => false,
                "status" => 500,
            ], 500);
        }
        return response()->json($property->fresh(), 200);    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Property $property
     * @return JsonResponse
     */
    public function destroy(Property $property): JsonResponse
    {
        if (! auth()->user()->can(Permissions::PERM_DELETE_TRANSACTIONS_PROPERTIES)) {
            abort(403, __('notifications.unauthorized_to_delete_transaction', []));
        }
        $property->update([
            "transactions" => [],
            "customer" => '',
        ]);
        return response()->json($property->fresh(), 200);
    }
}
