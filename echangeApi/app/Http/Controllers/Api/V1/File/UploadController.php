<?php

namespace App\Http\Controllers\Api\V1\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\File\UploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UploadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return JsonResponse
     */
    public function upload(string $resource, int $id, string $field, UploadRequest $request)
    {
        // Check if path is allowed
        if (auth()->check()) {
            // TODO: Check if user has permissions
            $path = "public/{$resource}/{$id}/{$field}";
            // Upload the image and return the path
            /** @var string $path */
            $path = Storage::put($path, $request->file('attachment'));
            $url = URL::asset(Storage::url($path), true);

            return response()->json(compact('url', 'path'), 201);
        }
        abort(400);
    }
}
