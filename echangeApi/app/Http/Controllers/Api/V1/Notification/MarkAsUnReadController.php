<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Requests\Api\V1\Notification\MarkAsUnReadRequest;
use App\Models\Notification;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class MarkAsUnReadController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function markAsUnRead(MarkAsUnReadRequest $request)
    {
        $ids = $request->input();
        foreach ($ids as $id) {
            $notification = Notification::find($id);
            $notification->markAsUnRead();
            $notification->save();
        }

        return $this->reply()->content([]);
    }
}
