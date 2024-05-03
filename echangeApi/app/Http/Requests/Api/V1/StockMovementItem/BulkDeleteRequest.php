<?php

namespace App\Http\Requests\Api\V1\StockMovementItem;

use App\Constants\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can(Permissions::PERM_CREATE_STOCK_MOVEMENTS);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'stockMovement' => [
                'required',
                'exists:stock_movements,id',
            ],
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:stock_movement_items,id'],
        ];
    }
}
