<?php

namespace App\Http\Requests\Api\V1\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class Toggle2FARequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
