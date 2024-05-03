<?php

namespace App\Http\Requests\Api\V1\StockMovement;

use App\Constants\Permissions;
use App\Models\StockMovement;
use Illuminate\Foundation\Http\FormRequest;

class CancelStockMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var StockMovement */
        $record = $this->route('record');

        return $this->user()->canAccessModelWithPermission($record, Permissions::PERM_EDIT_STOCK_MOVEMENTS);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
