<?php

namespace App\Http\Controllers\Api\V1\Import;

use App\Http\Requests\Api\V1\Import\RunRequest;
use App\Models\Import;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\Response;

class CancelController extends JsonApiController
{
    public function cancel(Import $import, RunRequest $request): Response
    {
        if ($import->status === Import::STATUS_DRAFT) {
            $import->status = Import::STATUS_CANCELED;
            $import->save();
        }

        return $this->reply()->content($import);
    }
}
