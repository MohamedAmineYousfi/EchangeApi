<?php

namespace App\Http\Requests\Api\V1\Auth;

use http\Env\Request;
use Illuminate\Foundation\Http\FormRequest;

class CodeVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['required', 'numeric', 'digits:6', 'exists:users,two_fa_code'],
        ];
    }
}
