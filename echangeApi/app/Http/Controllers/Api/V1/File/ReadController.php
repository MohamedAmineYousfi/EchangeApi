<?php

namespace App\Http\Controllers\Api\V1\File;

use App\Http\Requests\Api\V1\File\ReadRequest;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class ReadController extends JsonApiController
{
    /**
     * @return JsonResponse|Response
     */
    public function readFile($modelName, $modelId, $field, $filename, ReadRequest $request)
    {
        $filePath = storage_path("app/public/{$modelName}/{$modelId}/{$field}/{$filename}");

        if (! File::exists($filePath)) {
            abort(404, 'file not found');
        }

        $fileContent = File::get($filePath);
        $contentType = File::mimeType($filePath);

        return response($fileContent, 200)
            ->header('Content-Type', $contentType)
            ->header('Access-Control-Allow-Origin', '*');
    }
}
