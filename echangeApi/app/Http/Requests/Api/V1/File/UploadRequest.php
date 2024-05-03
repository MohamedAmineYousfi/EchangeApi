<?php

namespace App\Http\Requests\Api\V1\File;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadRequest extends FormRequest
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
            'attachment' => [
                'required',
                File::types([
                    // images
                    'png',
                    'jpeg',
                    'jpg',
                    'svg',
                    'webp',
                    // documents
                    'doc',
                    'docx',
                    'docm',
                    'docb',
                    'ods',
                    'pdf',
                    'xls',
                    'xltx',
                    'xltm',
                    'xlsx',
                    'xlsm',
                    'xlsb',
                    'pptx',
                    'pptm',
                    'potx',
                    'csv',
                    'txt',
                    // audios/videos
                    'mp4',
                    'mp3',
                    // compressed
                    'zip',

                    'csv',
                    'xlsx',
                    'xls',
                    'xlsm',
                    'xlsb',
                    'ods',
                ])->max(128000),
            ],
        ];
    }
}
